Chart.register(ChartDataLabels);

const chartInstances = {};

/**
 * FORMAT HELPERS
 */
function formatValue(value, format) {
    switch (format) {
        case 'currency':
            return `₱${Number(value).toLocaleString('en-PH')}`;
        case 'percent':
            return `${value}%`;
        case 'solds':
            return `${value} ${value > 1 ? 'solds' : 'sold'}`;
        default:
            return value;
    }
}

function createChart({
    canvasId,
    type = 'bar',
    labels = [],
    datasets = [],
    title = '',
    options = {}
}) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return;

    // destroy old instance
    if (chartInstances[canvasId]) {
        chartInstances[canvasId].destroy();
    }

    const isPie = ['pie', 'doughnut'].includes(type);

    chartInstances[canvasId] = new Chart(ctx, {
        type,
        data: {
            labels,
            datasets: datasets.map(ds => ({
                ...ds,
                tension: type === 'line' ? (ds.tension ?? 0.4) : 0
            }))
        },

        options: {
            responsive: true,
            maintainAspectRatio: false,

            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        font: {
                            size: options.legendFontSize || 14
                        }
                    }
                },

                title: {
                    display: !!title,
                    text: title,
                    font: {
                        size: options.titleFontSize || 18
                    }
                },

                // TOOLTIP
                tooltip: {
                    callbacks: {
                        label: function (context) {
                            const ds = context.dataset;
                            const value = context.raw;
                            const label = context.label || ds.label || '';

                            return `${label}: ${formatValue(value, ds.format)}`;
                        }
                    }
                },

                // DATALABELS
                datalabels: {
                    display: true,
                    color: '#000',
                    font: {
                        weight: 'bold',
                        size: 12
                    },
                    formatter: function (value, context) {
                        const ds = context.dataset;
                        return formatValue(value, ds.format);
                    }
                },

                ...(options.plugins || {})
            },

            // AXES (skip for pie/doughnut)
            scales: !isPie
                ? {
                      x: {
                          ticks: {
                              font: {
                                  size: options.axisFontSize || 12
                              }
                          }
                      },
                      y: {
                          beginAtZero: true,
                          ticks: {
                              font: {
                                  size: options.axisFontSize || 12
                              }
                          }
                      }
                  }
                : {},

            ...options
        }
    });

    return chartInstances[canvasId];
}