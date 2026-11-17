/* =========================================================
   DASHBOARD: gráfico de crescimento (Apache ECharts)
   Os números vêm do controller, pela variável window.DASHBOARD
   (rotulos, usuarias e profissionais).
   ========================================================= */
document.addEventListener('DOMContentLoaded', function () {
    const caixa = document.getElementById('graficoCrescimento');
    const dados = window.DASHBOARD;

    if (!caixa || !dados || typeof echarts === 'undefined') {
        return;
    }

    const grafico = echarts.init(caixa);
    const cinza = { fontSize: 11, color: '#777' };

    grafico.setOption({
        textStyle: { fontFamily: 'Nunito, Arial, sans-serif', color: '#777' },
        color: ['#EBA5B4', '#9E182B'],
        tooltip: { trigger: 'axis' },
        legend: {
            data: ['Usuárias', 'Profissionais'],
            right: 0, top: 0, icon: 'circle', itemWidth: 8, itemHeight: 8, textStyle: cinza
        },
        grid: { left: 0, right: 22, top: 36, bottom: 0, containLabel: true },
        xAxis: {
            type: 'category',
            boundaryGap: false,
            data: dados.rotulos,
            axisLine: { show: false },
            axisTick: { show: false },
            axisLabel: cinza
        },
        yAxis: {
            type: 'value',
            minInterval: 1,
            axisLabel: cinza,
            splitLine: { lineStyle: { color: '#ececec' } }
        },
        series: [
            {
                name: 'Usuárias', type: 'line', smooth: true, symbol: 'circle', symbolSize: 6,
                lineStyle: { width: 2 }, data: dados.usuarias
            },
            {
                name: 'Profissionais', type: 'line', smooth: true, symbol: 'circle', symbolSize: 6,
                lineStyle: { width: 2 }, data: dados.profissionais
            }
        ]
    });

    // Ajusta o gráfico quando a caixa muda de tamanho
    new ResizeObserver(function () {
        grafico.resize();
    }).observe(caixa);
});
