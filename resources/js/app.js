import './bootstrap';
import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';
import '.login-animation.js';

// Make Chart.js available globally
window.Chart = Chart;
Chart.start();

// Make Alpine.js available globally
window.Alpine = Alpine;
Alpine.start();

// Auto-hide alerts after 10 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 10000);
    });

    // Initialize tooltips
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

    // Initialize popovers
    const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
    const popoverList = [...popoverTriggerList].map(popoverTriggerEl => new bootstrap.Popover(popoverTriggerEl));

    // Confirm delete dialogs
    document.querySelectorAll('form[data-confirm]').forEach(form => {
        form.addEventListener('submit', function(e) {
            const message = this.dataset.confirm || 'Вы уверены?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });

    // Dynamic form collections (for adding/removing form rows)
    initDynamicForms();
});

// Initialize dynamic forms
function initDynamicForms() {
    document.querySelectorAll('[data-dynamic-form]').forEach(container => {
        const addButton = container.querySelector('[data-add]');
        const template = container.querySelector('[data-template]');
        
        if (addButton && template) {
            let index = container.querySelectorAll('[data-item]').length;
            
            addButton.addEventListener('click', function() {
                const newItem = template.cloneNode(true);
                newItem.removeAttribute('data-template');
                newItem.setAttribute('data-item', '');
                newItem.classList.remove('d-none');
                
                // Update indices in form fields
                newItem.innerHTML = newItem.innerHTML.replace(/\[__index__\]/g, `[${index}]`);
                
                container.insertBefore(newItem, addButton);
                index++;
            });
        }
        
        // Remove item functionality
        container.addEventListener('click', function(e) {
            if (e.target.closest('[data-remove]')) {
                e.target.closest('[data-item]').remove();
            }
        });
    });
}

// Chart.js default configuration
if (window.Chart) {
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#666';
}

// FullCalendar initialization helper
window.initCalendar = function(elementId, events) {
    const calendarEl = document.getElementById(elementId);
    if (calendarEl) {
        const calendar = new FullCalendar.Calendar(calendarEl, {
            plugins: ['dayGrid', 'timeGrid', 'list'],
            initialView: 'dayGridMonth',
            locale: 'uk',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listWeek'
            },
            events: events,
            eventClick: function(info) {
                // Handle event click
                if (info.event.url) {
                    window.open(info.event.url, '_blank');
                    info.jsEvent.preventDefault();
                }
            }
        });
        calendar.render();
        return calendar;
    }
    
};