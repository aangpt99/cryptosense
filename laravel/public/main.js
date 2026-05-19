// ======================================================
// GLOBAL UTIL — HIGHLIGHT SCROLL
// ======================================================
function scrollHighlight(amount) {
  const el = document.getElementById("highlightScroll");
  if (!el) return;

  el.scrollBy({
    left: amount,
    behavior: 'smooth'
  });
}


// ======================================================
// DOM READY
// ======================================================
document.addEventListener("DOMContentLoaded", () => {

  const path = window.location.pathname;

  // ====================================================
  // COIN TICKER + NAV MENU (GLOBAL)
  // ====================================================
  let selectedSymbol = "BTC";

  document.querySelectorAll(".coin[data-symbol]").forEach(coin => {
    coin.addEventListener("click", () => {
      selectedSymbol = coin.dataset.symbol;
    });
  });

  document.querySelectorAll(".nav-link-menu").forEach(link => {
    const page = link.dataset.page;
    if (!page) return;

    link.addEventListener("click", e => {
      e.preventDefault();

      if (page === "distribution") {
        window.location.href = `/coin/${selectedSymbol}/distribution`;
      } else if (page === "chart") {
        window.location.href = `/coin/${selectedSymbol}/chart`;
      } else if (page === "news") {
        window.location.href = `/coin/${selectedSymbol}/news`;
      }
    });
  });


  // ====================================================
  // NEWS PAGE — TRENDING & COIN NEWS
  // ====================================================
  const newsBox = document.getElementById("news-list");
  const selectSentiment = document.getElementById("filterSentiment");

  if (newsBox) {
    newsBox.addEventListener("click", e => {
      const card = e.target.closest(".news-card");
      if (!card) return;
      const url = card.dataset.url;
      if (url) window.open(url, "_blank");
    });
  }

  if (newsBox && selectSentiment) {
    selectSentiment.addEventListener("change", () => {
      const filter = selectSentiment.value;

      fetch(`/trending/filter?sentiment=${encodeURIComponent(filter)}`, {
        headers: { "X-Requested-With": "XMLHttpRequest" }
      })
        .then(r => r.json())
        .then(data => {
          newsBox.innerHTML = "";

          if (!data.articles || data.articles.length === 0) {
            newsBox.innerHTML = `
              <div class="alert alert-info w-100 text-center">
                Tidak ada berita untuk kategori ini.
              </div>`;
            return;
          }

          data.articles.forEach(a => {
            const score = Number(a.sentiment_score || 0).toFixed(3);
            const thumb = a.thumbnail && a.thumbnail.trim()
              ? a.thumbnail
              : "/static/no-image.png";

            newsBox.insertAdjacentHTML("beforeend", `
              <div class="news-card" data-url="${a.url}">
                <img src="${thumb}">
                <div class="card-body">
                  <h6 class="title-truncate-2">${a.title}</h6>
                  <small class="text-muted d-block mb-2">
                    ${a.published_at_fmt} | ${a.source} | ${a.coin}
                  </small>
                  <div class="sentiment-badge sentiment-${a.sentiment}">
                    ${a.sentiment} (${score})
                  </div>
                </div>
              </div>
            `);
          });
        });
    });
  }


  // ====================================================
  // DISTRIBUTION PAGE — PERIOD FILTER (ISOLATED)
  // ====================================================
  if (path.includes("/distribution")) {
    const periodFilter = document.getElementById("filterPeriod");
    if (periodFilter) {
      periodFilter.addEventListener("change", e => {
        const p = e.target.value;
        const symbol = path.split("/")[2] || "";
        window.location.href = `/coin/${symbol}/distribution?period=${p}`;
      });
    }
  }


  // ====================================================
  // DISTRIBUTION PAGE — PIE CHART
  // ====================================================
  const pieCanvas = document.getElementById("pieChart");
  if (pieCanvas && window.Chart) {
    const pos = Number(pieCanvas.dataset.pos || 0);
    const neg = Number(pieCanvas.dataset.neg || 0);
    const neu = Number(pieCanvas.dataset.neu || 0);

    new Chart(pieCanvas, {
      type: "doughnut",
      data: {
        labels: ["Positif", "Negatif", "Netral"],
        datasets: [{
          data: [pos, neg, neu],
          backgroundColor: ["#16a34a", "#dc2626", "#6b7280"],
          borderWidth: 0
        }]
      },
      options: {
        plugins: { legend: { position: "bottom" } },
        cutout: "60%"
      }
    });
  }


  // ====================================================
  // CHART PAGE — LINE CHART (ISOLATED)
  // ====================================================
  if (path.includes("/chart")) {

    const chartCanvas = document.getElementById("sentimentChart");
    if (!chartCanvas || !window.Chart) return;

    let labels = [], posData = [], negData = [], neuData = [];

    try {
      labels  = JSON.parse(chartCanvas.dataset.labels || "[]");
      posData = JSON.parse(chartCanvas.dataset.pos || "[]");
      negData = JSON.parse(chartCanvas.dataset.neg || "[]");
      neuData = JSON.parse(chartCanvas.dataset.neu || "[]");
    } catch {}

    const colors = {
      positive: "rgba(22,163,74,1)",
      negative: "rgba(220,38,38,1)",
      neutral:  "rgba(107,114,128,1)"
    };

    function makeGradient(ctx, color) {
      const g = ctx.createLinearGradient(0, 0, 0, 260);
      g.addColorStop(0, color.replace("1)", "0.22)"));
      g.addColorStop(1, color.replace("1)", "0)"));
      return g;
    }

    const ctx = chartCanvas.getContext("2d");

    const chart = new Chart(ctx, {
      type: "line",
      data: {
        labels,
        datasets: [{
          data: posData,
          borderColor: colors.positive,
          backgroundColor: makeGradient(ctx, colors.positive),
          borderWidth: 3,
          tension: 0.45,
          pointRadius: 4,
          pointHoverRadius: 6,
          pointBackgroundColor: "#fff",
          pointBorderColor: colors.positive,
          pointBorderWidth: 2
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        layout: {
          padding: { left: 14, right: 14, top: 8, bottom: 32 }
        },
        scales: {
          x: {
            ticks: {
              autoSkip: true,
              maxTicksLimit: 5,
              color: "#6b7280",
              callback: (_, index) => {
                const raw = labels[index];
                if (!raw) return "";
                const d = new Date(raw);
                return d.toLocaleDateString("id-ID", {
                  day: "2-digit",
                  month: "short"
                });
              }
            },
            grid: { display: false }
          },
          y: {
            min: 0,
            max: 1,
            ticks: {
              stepSize: 0.1,
              padding: 6,
              callback: v => v.toFixed(1),
              color: "#6b7280"
            },
            grid: { color: "#e5e7eb" }
          }
        },
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: "#111827",
            titleColor: "#fff",
            bodyColor: "#e5e7eb",
            padding: 10,
            displayColors: false
          }
        }
      }
    });

    document.querySelectorAll(".filter-btns button").forEach(btn => {
      btn.addEventListener("click", () => {
        document.querySelectorAll(".filter-btns button")
          .forEach(b => b.classList.remove("active"));
        btn.classList.add("active");

        const t = btn.dataset.type;
        const data = t === "positive" ? posData :
                     t === "negative" ? negData : neuData;

        chart.data.datasets[0].data = data;
        chart.data.datasets[0].borderColor = colors[t];
        chart.data.datasets[0].backgroundColor = makeGradient(ctx, colors[t]);
        chart.data.datasets[0].pointBorderColor = colors[t];
        chart.update();
      });
    });

    const periodSelect = document.getElementById("periodSelect");
    if (periodSelect) {
      periodSelect.addEventListener("change", () => {
        const p = periodSelect.value;
        const symbol = path.split("/")[2] || "";
        window.location.href = `/coin/${symbol}/chart?period=${p}`;
      });
    }
  }

});
