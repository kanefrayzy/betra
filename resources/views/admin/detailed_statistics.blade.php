@extends('panel')
@php $baseUrl = 'panel8808'; @endphp

@section('content')

<div class="space-y-8 animate-fade-in">
    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl p-8 text-white">
        <h1 class="text-3xl font-bold mb-2">Детальная статистика</h1>
        <p class="text-indigo-100">Все суммы отображены в USD</p>
    </div>

    <!-- Controls Section -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-100 dark:border-gray-700">
        <div class="flex flex-col space-y-6">
            <!-- Date Range Picker -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="space-y-2">
                    <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Дата начала</label>
                    <div class="relative">
                        <input
                            type="date"
                            id="start_date"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200"
                            value="{{ \Carbon\Carbon::now()->subDays(30)->toDateString() }}"
                        >
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Дата окончания</label>
                    <div class="relative">
                        <input
                            type="date"
                            id="end_date"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200"
                            value="{{ \Carbon\Carbon::now()->toDateString() }}"
                        >
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Действие</label>
                    <button
                        class="w-full bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-200 transform hover:scale-105 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 shadow-lg"
                        id="show-statistics"
                    >
                        <div class="flex items-center justify-center space-x-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                            </svg>
                            <span>Показать статистику</span>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Quick Range Buttons -->
            <div class="flex flex-wrap gap-3">
                <button
                    class="px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-medium rounded-xl transition-all duration-200 transform hover:scale-105 shadow-lg"
                    data-range="yesterday"
                >
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.707-10.293a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L9.414 11H13a1 1 0 100-2H9.414l1.293-1.293z" clip-rule="evenodd"/>
                        </svg>
                        <span>За вчера</span>
                    </div>
                </button>

                <button
                    class="px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white font-medium rounded-xl transition-all duration-200 transform hover:scale-105 shadow-lg"
                    data-range="week"
                >
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                        </svg>
                        <span>За неделю</span>
                    </div>
                </button>

                <button
                    class="px-6 py-3 bg-gradient-to-r from-purple-500 to-pink-600 hover:from-purple-600 hover:to-pink-700 text-white font-medium rounded-xl transition-all duration-200 transform hover:scale-105 shadow-lg"
                    data-range="month"
                >
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                        </svg>
                        <span>За месяц</span>
                    </div>
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Container -->
    <div id="statistics-container" class="space-y-8 hidden">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Profit Card -->
            <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl p-6 text-white shadow-2xl hover:shadow-3xl transform hover:-translate-y-1 transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-white bg-opacity-20 rounded-xl backdrop-blur">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"/>
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="text-right">
                        <div class="w-3 h-3 bg-green-300 rounded-full animate-pulse"></div>
                    </div>
                </div>
                <h3 class="text-lg font-medium text-green-100 mb-2">Общий профит за период</h3>
                <p class="text-3xl font-bold" id="profit">$0.00</p>
            </div>

            <!-- Deposits Card -->
            <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl p-6 text-white shadow-2xl hover:shadow-3xl transform hover:-translate-y-1 transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-white bg-opacity-20 rounded-xl backdrop-blur">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="text-right">
                        <div class="w-3 h-3 bg-blue-300 rounded-full"></div>
                    </div>
                </div>
                <h3 class="text-lg font-medium text-blue-100 mb-2">Общая сумма пополнений</h3>
                <p class="text-3xl font-bold" id="total-deposits">$0.00</p>
            </div>

            <!-- Withdrawals Card -->
            <div class="bg-gradient-to-br from-red-500 to-pink-600 rounded-2xl p-6 text-white shadow-2xl hover:shadow-3xl transform hover:-translate-y-1 transition-all duration-300">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-white bg-opacity-20 rounded-xl backdrop-blur">
                        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="text-right">
                        <div class="w-3 h-3 bg-red-300 rounded-full"></div>
                    </div>
                </div>
                <h3 class="text-lg font-medium text-red-100 mb-2">Общая сумма выводов</h3>
                <p class="text-3xl font-bold" id="total-withdrawals">$0.00</p>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-lg border border-gray-100 dark:border-gray-700">
            <div class="flex items-center mb-6">
                <div class="w-8 h-8 bg-gradient-to-r from-orange-500 to-red-600 rounded-lg flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">График транзакций</h2>
            </div>

            <div class="relative">
                <canvas id="transactionsChart" class="w-full h-96"></canvas>
                <div id="chart-loading" class="absolute inset-0 flex items-center justify-center bg-white dark:bg-gray-800 bg-opacity-90" style="display:none!important">
                    <div class="flex items-center space-x-3">
                        <div class="animate-spin w-6 h-6 border-2 border-blue-500 border-t-transparent rounded-full"></div>
                        <span class="text-gray-600 dark:text-gray-400">Загрузка графика...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading State -->
    <div id="loading-state" class="hidden">
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 shadow-lg border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-center space-x-4">
                <div class="animate-spin w-8 h-8 border-4 border-blue-500 border-t-transparent rounded-full"></div>
                <span class="text-lg text-gray-600 dark:text-gray-400">Загрузка статистики...</span>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function() {
    let chart;

    function showLoading() {
        document.getElementById('loading-state').classList.remove('hidden');
        document.getElementById('statistics-container').classList.add('hidden');
    }

    function hideLoading() {
        document.getElementById('loading-state').classList.add('hidden');
    }

    function getStatistics() {
        showLoading();
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;

        fetch(`/qwdkox1i20/detailed-statistics/data?start_date=${startDate}&end_date=${endDate}`)
            .then(response => response.json())
            .then(data => {
                displayStatistics(data.statistics);
                createChart(data.chart_data);
                hideLoading();
                document.getElementById('statistics-container').classList.remove('hidden');

                // Smooth scroll to results
                document.getElementById('statistics-container').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            })
            .catch(error => {
                console.error('Error:', error);
                hideLoading();

                // Show error toast
                const errorToast = document.createElement('div');
                errorToast.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-4 rounded-xl shadow-lg z-50 transform translate-x-full transition-transform duration-300';
                errorToast.innerHTML = `
                    <div class="flex items-center space-x-3">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <span>Произошла ошибка при получении статистики</span>
                    </div>
                `;
                document.body.appendChild(errorToast);

                setTimeout(() => errorToast.classList.remove('translate-x-full'), 100);
                setTimeout(() => {
                    errorToast.classList.add('translate-x-full');
                    setTimeout(() => document.body.removeChild(errorToast), 300);
                }, 5000);
            });
    }

    function displayStatistics(stats) {
        const totalDeposits = parseFloat(stats.total_deposits) || 0;
        const totalWithdrawals = parseFloat(stats.total_withdrawals) || 0;
        const profit = totalDeposits - totalWithdrawals;

        // Animate numbers
        animateValue('total-deposits', 0, totalDeposits, 1500, '$');
        animateValue('total-withdrawals', 0, totalWithdrawals, 1500, '$');
        animateValue('profit', 0, profit, 1500, '$');
    }

    function animateValue(elementId, start, end, duration, prefix = '') {
        const element = document.getElementById(elementId);
        const startTimestamp = performance.now();

        function step(timestamp) {
            const elapsed = timestamp - startTimestamp;
            const progress = Math.min(elapsed / duration, 1);
            const current = start + (end - start) * easeOutQuart(progress);

            element.textContent = `${prefix}${current.toFixed(2)}`;

            if (progress < 1) {
                requestAnimationFrame(step);
            }
        }

        requestAnimationFrame(step);
    }

    function easeOutQuart(t) {
        return 1 - Math.pow(1 - t, 4);
    }

    function createChart(data) {
        const ctx = document.getElementById('transactionsChart').getContext('2d');

        if (chart) {
            chart.destroy();
        }

        // Show chart loading
        document.getElementById('chart-loading').classList.remove('hidden');

        setTimeout(() => {
            chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.map(item => item.date),
                    datasets: [{
                        label: 'Пополнения (USD)',
                        data: data.map(item => item.deposits),
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: 'rgb(59, 130, 246)',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 8
                    }, {
                        label: 'Выводы (USD)',
                        data: data.map(item => item.withdrawals),
                        borderColor: 'rgb(239, 68, 68)',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: 'rgb(239, 68, 68)',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                                font: {
                                    size: 12,
                                    weight: 'bold'
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleColor: '#ffffff',
                            bodyColor: '#ffffff',
                            borderColor: 'rgba(255, 255, 255, 0.2)',
                            borderWidth: 1,
                            cornerRadius: 8,
                            displayColors: true,
                            padding: 12
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                color: 'rgba(156, 163, 175, 0.2)',
                                drawBorder: false
                            },
                            ticks: {
                                color: '#6b7280',
                                font: {
                                    size: 11
                                }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(156, 163, 175, 0.2)',
                                drawBorder: false
                            },
                            ticks: {
                                color: '#6b7280',
                                font: {
                                    size: 11
                                },
                                callback: function(value) {
                                    return '$' + value.toFixed(2);
                                }
                            }
                        }
                    },
                    elements: {
                        point: {
                            hoverBackgroundColor: '#ffffff'
                        }
                    }
                }
            });

            document.getElementById('chart-loading').classList.add('hidden');
        }, 500);
    }

    function setDateRange(range) {
        let startDate = new Date();
        let endDate = new Date();

        switch(range) {
            case 'yesterday':
                startDate.setDate(startDate.getDate() - 1);
                endDate.setDate(endDate.getDate() - 1);
                break;
            case 'week':
                startDate.setDate(startDate.getDate() - 7);
                break;
            case 'month':
                startDate.setMonth(startDate.getMonth() - 1);
                break;
        }

        document.getElementById('start_date').value = startDate.toISOString().split('T')[0];
        document.getElementById('end_date').value = endDate.toISOString().split('T')[0];

        getStatistics();
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('show-statistics').addEventListener('click', getStatistics);

        document.querySelectorAll('button[data-range]').forEach(button => {
            button.addEventListener('click', function() {
                // Add loading state to button
                const originalText = this.innerHTML;
                this.innerHTML = `
                    <div class="flex items-center space-x-2">
                        <div class="animate-spin w-4 h-4 border-2 border-white border-t-transparent rounded-full"></div>
                        <span>Загрузка...</span>
                    </div>
                `;
                this.disabled = true;

                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.disabled = false;
                }, 2000);

                setDateRange(this.getAttribute('data-range'));
            });
        });
    });
})();
</script>

@endsection
