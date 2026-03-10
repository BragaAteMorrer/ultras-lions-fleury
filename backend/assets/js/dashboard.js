// public/assets/js/dashboard.js

document.addEventListener('DOMContentLoaded', () => {
    const canvas = document.getElementById('postsChart');
    if (!canvas) return;

    let labels = [];
    let values = [];

    try {
        labels = JSON.parse(canvas.dataset.labels || '[]');
        values = JSON.parse(canvas.dataset.values || '[]');
    } catch (e) {
        console.error('Données de graph invalides', e);
        return;
    }

    if (!labels.length || !values.length) {
        console.warn('Pas de données pour le graphique des articles.');
        return;
    }

    const ctx = canvas.getContext('2d');

    // Chart.js est chargé via CDN et fournit la variable globale "Chart"
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Articles publiés',
                data: values,
                borderColor: '#D40000',
                backgroundColor: 'rgba(212,0,0,0.25)',
                borderWidth: 2,
                pointRadius: 3,
                pointHoverRadius: 5,
                tension: 0.3,
                fill: true,
            }]
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                x: {
                    ticks: { color: '#ffffff' },
                    grid: { color: 'rgba(255,255,255,0.1)' }
                },
                y: {
                    ticks: { color: '#ffffff' },
                    grid: { color: 'rgba(255,255,255,0.1)' }
                }
            },
            plugins: {
                legend: {
                    labels: {
                        color: '#ffffff',
                        font: { size: 13 }
                    }
                }
            }
        }
    });
});

function initEventCategoryToggle() {
    const category = document.querySelector('#Event_category, #event_category, [name$="[category]"]');
    const title = document.querySelector('#Event_title, #event_title, [name$="[title]"]');
    const opponent = document.querySelector('#Event_opponent, #event_opponent, [name$="[opponent]"]');
    const matchLocation = document.querySelector('#Event_matchLocation, #event_matchLocation, [name$="[matchLocation]"]');
    const season = document.querySelector('#Event_season, #event_season, [name$="[season]"]');
    const journee = document.querySelector('#Event_journee, #event_journee, [name$="[journee]"]');

    if (!category || !season || !journee || !title || !opponent || !matchLocation) return;

    const findFieldWrapper = (input, propertyName) => {
        return input.closest(`[data-property-name="${propertyName}"], .form-group, .field-choice, .field-integer, .field-text, .mb-3`) || input.parentElement;
    };

    const titleWrapper = findFieldWrapper(title, 'title');
    const opponentWrapper = findFieldWrapper(opponent, 'opponent');
    const locationWrapper = findFieldWrapper(matchLocation, 'matchLocation');
    const seasonWrapper = findFieldWrapper(season, 'season');
    const journeeWrapper = findFieldWrapper(journee, 'journee');

    const update = () => {
        const isEvenement = category.value === 'evenement';
        const hideForEvenement = isEvenement ? 'none' : '';
        const hideForPhotoMatch = isEvenement ? '' : 'none';

        if (titleWrapper) titleWrapper.style.display = hideForPhotoMatch;
        if (opponentWrapper) opponentWrapper.style.display = hideForEvenement;
        if (locationWrapper) locationWrapper.style.display = hideForEvenement;
        if (seasonWrapper) seasonWrapper.style.display = hideForEvenement;
        if (journeeWrapper) journeeWrapper.style.display = hideForEvenement;

        title.disabled = false;
        opponent.disabled = isEvenement;
        matchLocation.disabled = isEvenement;

        season.disabled = isEvenement;
        journee.disabled = isEvenement;

        if (isEvenement) {
            opponent.value = '';
            matchLocation.value = '';
            season.value = '';
            journee.value = '';
        }
    };

    category.addEventListener('change', update);
    update();
}

document.addEventListener('DOMContentLoaded', initEventCategoryToggle);
document.addEventListener('turbo:load', initEventCategoryToggle);
