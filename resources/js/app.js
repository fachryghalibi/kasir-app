import './bootstrap';

// POS System JavaScript
document.addEventListener('DOMContentLoaded', function() {
    console.log('🛒 POS System Loaded with Tailwind CSS 4');
    
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert-auto-dismiss');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
    
    // Initialize tooltips jika ada
    initializeTooltips();
    
    // Barcode scanner listener
    initializeBarcodeScanner();
});

// Tooltip initialization
function initializeTooltips() {
    const tooltips = document.querySelectorAll('[data-tooltip]');
    tooltips.forEach(el => {
        el.addEventListener('mouseenter', showTooltip);
        el.addEventListener('mouseleave', hideTooltip);
    });
}

function showTooltip(e) {
    const text = e.target.getAttribute('data-tooltip');
    const tooltip = document.createElement('div');
    tooltip.className = 'absolute z-50 px-3 py-2 text-sm text-white bg-gray-900 rounded-lg shadow-lg';
    tooltip.textContent = text;
    tooltip.id = 'tooltip';
    document.body.appendChild(tooltip);
    
    const rect = e.target.getBoundingClientRect();
    tooltip.style.top = `${rect.bottom + 5}px`;
    tooltip.style.left = `${rect.left}px`;
}

function hideTooltip() {
    const tooltip = document.getElementById('tooltip');
    if (tooltip) tooltip.remove();
}

// Barcode Scanner
function initializeBarcodeScanner() {
    let barcode = '';
    let barcodeTimeout;
    
    document.addEventListener('keypress', (e) => {
        // Jika di input field, abaikan
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
            return;
        }
        
        clearTimeout(barcodeTimeout);
        
        if (e.key === 'Enter') {
            if (barcode.length > 5) {
                handleBarcodeScanned(barcode);
                barcode = '';
            }
        } else {
            barcode += e.key;
            barcodeTimeout = setTimeout(() => {
                barcode = '';
            }, 100);
        }
    });
}

function handleBarcodeScanned(code) {
    console.log('Barcode scanned:', code);
    // Trigger custom event
    document.dispatchEvent(new CustomEvent('barcode-scanned', { 
        detail: { barcode: code } 
    }));
}