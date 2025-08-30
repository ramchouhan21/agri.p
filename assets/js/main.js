/**
 * Main Application JavaScript
 * Handles UI interactions, form validations, and other frontend functionality
 */

document.addEventListener('DOMContentLoaded', () => {
    // Initialize all tooltips
    const initTooltips = () => {
        const tooltipTriggers = document.querySelectorAll('[data-tooltip]');
        
        tooltipTriggers.forEach(trigger => {
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            tooltip.textContent = trigger.getAttribute('data-tooltip');
            document.body.appendChild(tooltip);
            
            const updateTooltipPosition = (e) => {
                const rect = trigger.getBoundingClientRect();
                const tooltipRect = tooltip.getBoundingClientRect();
                
                let top, left;
                const position = trigger.getAttribute('data-tooltip-pos') || 'top';
                const offset = 8;
                
                switch (position) {
                    case 'top':
                        top = rect.top - tooltipRect.height - offset;
                        left = rect.left + (rect.width - tooltipRect.width) / 2;
                        break;
                    case 'right':
                        top = rect.top + (rect.height - tooltipRect.height) / 2;
                        left = rect.right + offset;
                        break;
                    case 'bottom':
                        top = rect.bottom + offset;
                        left = rect.left + (rect.width - tooltipRect.width) / 2;
                        break;
                    case 'left':
                        top = rect.top + (rect.height - tooltipRect.height) / 2;
                        left = rect.left - tooltipRect.width - offset;
                        break;
                }
                
                tooltip.style.top = `${Math.max(0, top)}px`;
                tooltip.style.left = `${Math.max(0, left)}px`;
            };
            
            trigger.addEventListener('mouseenter', (e) => {
                tooltip.classList.add('show');
                updateTooltipPosition(e);
            });
            
            trigger.addEventListener('mouseleave', () => {
                tooltip.classList.remove('show');
            });
            
            trigger.addEventListener('mousemove', updateTooltipPosition);
        });
    };
    
    // Initialize all dropdowns
    const initDropdowns = () => {
        document.querySelectorAll('[data-dropdown]').forEach(dropdown => {
            const toggle = dropdown.querySelector('[data-dropdown-toggle]');
            const menu = dropdown.querySelector('[data-dropdown-menu]');
            
            if (toggle && menu) {
                toggle.addEventListener('click', (e) => {
                    e.stopPropagation();
                    const isOpen = dropdown.getAttribute('data-dropdown-open') === 'true';
                    
                    // Close all other dropdowns
                    document.querySelectorAll('[data-dropdown]').forEach(d => {
                        if (d !== dropdown) {
                            d.setAttribute('data-dropdown-open', 'false');
                        }
                    });
                    
                    dropdown.setAttribute('data-dropdown-open', (!isOpen).toString());
                });
            }
        });
        
        // Close dropdowns when clicking outside
        document.addEventListener('click', (e) => {
            document.querySelectorAll('[data-dropdown]').forEach(dropdown => {
                if (!dropdown.contains(e.target)) {
                    dropdown.setAttribute('data-dropdown-open', 'false');
                }
            });
        });
    };
    
    // Initialize all modals
    const initModals = () => {
        // Open modal
        document.querySelectorAll('[data-modal-toggle]').forEach(button => {
            const modalId = button.getAttribute('data-modal-toggle');
            const modal = document.getElementById(modalId);
            
            if (modal) {
                button.addEventListener('click', () => {
                    modal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                });
            }
        });
        
        // Close modal
        document.querySelectorAll('[data-modal-hide]').forEach(button => {
            const modalId = button.getAttribute('data-modal-hide');
            const modal = document.getElementById(modalId) || button.closest('.modal');
            
            if (modal) {
                button.addEventListener('click', () => {
                    modal.classList.add('hidden');
                    document.body.style.overflow = '';
                });
            }
        });
        
        // Close modal when clicking outside
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                    document.body.style.overflow = '';
                }
            });
        });
    };
    
    // Initialize form validations
    const initForms = () => {
        document.querySelectorAll('form[data-validate]').forEach(form => {
            const inputs = form.querySelectorAll('[data-validate-field]');
            
            const validateField = (input) => {
                const value = input.value.trim();
                const fieldName = input.getAttribute('name') || input.getAttribute('id') || 'This field';
                const errorElement = input.nextElementSibling?.classList?.contains('error-message') 
                    ? input.nextElementSibling 
                    : null;
                
                // Required validation
                if (input.hasAttribute('required') && !value) {
                    showError(input, `${fieldName} is required`, errorElement);
                    return false;
                }
                
                // Email validation
                if (input.type === 'email' && value) {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(value)) {
                        showError(input, 'Please enter a valid email address', errorElement);
                        return false;
                    }
                }
                
                // Password confirmation
                if (input.hasAttribute('data-confirm')) {
                    const confirmField = form.querySelector(`[name="${input.getAttribute('data-confirm')}"]`);
                    if (confirmField && value !== confirmField.value) {
                        showError(input, 'Passwords do not match', errorElement);
                        return false;
                    }
                }
                
                // Min length
                if (input.hasAttribute('minlength')) {
                    const minLength = parseInt(input.getAttribute('minlength'));
                    if (value.length < minLength) {
                        showError(input, `Must be at least ${minLength} characters`, errorElement);
                        return false;
                    }
                }
                
                // Max length
                if (input.hasAttribute('maxlength') && value.length > parseInt(input.getAttribute('maxlength'))) {
                    input.value = value.slice(0, input.getAttribute('maxlength'));
                }
                
                // If all validations pass
                clearError(input, errorElement);
                return true;
            };
            
            const showError = (input, message, errorElement) => {
                input.classList.add('error');
                
                if (errorElement) {
                    errorElement.textContent = message;
                    errorElement.style.display = 'block';
                } else {
                    const errorMsg = document.createElement('div');
                    errorMsg.className = 'error-message text-red-500 text-sm mt-1';
                    errorMsg.textContent = message;
                    input.parentNode.insertBefore(errorMsg, input.nextSibling);
                }
            };
            
            const clearError = (input, errorElement) => {
                input.classList.remove('error');
                if (errorElement) {
                    errorElement.style.display = 'none';
                }
            };
            
            // Validate on blur
            inputs.forEach(input => {
                input.addEventListener('blur', () => validateField(input));
                input.addEventListener('input', () => {
                    if (input.classList.contains('error')) {
                        validateField(input);
                    }
                });
            });
            
            // Form submission
            form.addEventListener('submit', (e) => {
                let isValid = true;
                
                inputs.forEach(input => {
                    if (!validateField(input)) {
                        isValid = false;
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    
                    // Scroll to first error
                    const firstError = form.querySelector('.error');
                    if (firstError) {
                        firstError.scrollIntoView({ 
                            behavior: 'smooth', 
                            block: 'center' 
                        });
                    }
                }
            });
        });
    };
    
    // Initialize all tabs
    const initTabs = () => {
        document.querySelectorAll('[data-tabs]').forEach(tabsContainer => {
            const tabs = tabsContainer.querySelectorAll('[data-tab]');
            const tabContents = tabsContainer.querySelectorAll('[data-tab-content]');
            
            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    const tabId = tab.getAttribute('data-tab');
                    
                    // Update active tab
                    tabs.forEach(t => t.classList.remove('active'));
                    tab.classList.add('active');
                    
                    // Show corresponding content
                    tabContents.forEach(content => {
                        content.classList.toggle('hidden', content.getAttribute('data-tab-content') !== tabId);
                    });
                });
            });
        });
    };
    
    // Initialize all accordions
    const initAccordions = () => {
        document.querySelectorAll('[data-accordion]').forEach(accordion => {
            const trigger = accordion.querySelector('[data-accordion-trigger]');
            const content = accordion.querySelector('[data-accordion-content]');
            
            if (trigger && content) {
                trigger.addEventListener('click', () => {
                    const isOpen = accordion.getAttribute('data-accordion-open') === 'true';
                    
                    if (isOpen) {
                        content.style.maxHeight = '0';
                        accordion.setAttribute('data-accordion-open', 'false');
                    } else {
                        content.style.maxHeight = `${content.scrollHeight}px`;
                        accordion.setAttribute('data-accordion-open', 'true');
                    }
                });
                
                // Initialize height
                if (accordion.getAttribute('data-accordion-open') === 'true') {
                    content.style.maxHeight = `${content.scrollHeight}px`;
                } else {
                    content.style.maxHeight = '0';
                }
            }
        });
    };
    
    // Initialize all toasts
    const initToasts = () => {
        // Auto-remove toasts after delay
        const autoRemoveToasts = () => {
            document.querySelectorAll('.toast').forEach(toast => {
                const autoHide = toast.getAttribute('data-autohide') !== 'false';
                const delay = parseInt(toast.getAttribute('data-delay') || '5000');
                
                if (autoHide) {
                    setTimeout(() => {
                        toast.classList.add('opacity-0', 'translate-y-2');
                        setTimeout(() => toast.remove(), 150);
                    }, delay);
                }
                
                // Close button
                const closeButton = toast.querySelector('[data-toast-dismiss]');
                if (closeButton) {
                    closeButton.addEventListener('click', () => {
                        toast.classList.add('opacity-0', 'translate-y-2');
                        setTimeout(() => toast.remove(), 150);
                    });
                }
            });
        };
        
        // Initialize any toasts that are already in the DOM
        autoRemoveToasts();
        
        // Function to create and show a new toast
        window.showToast = (options) => {
            const { 
                title = '', 
                message = '', 
                type = 'info', 
                duration = 5000,
                position = 'top-right'
            } = options;
            
            const toast = document.createElement('div');
            toast.className = `toast toast-${position} ${type} flex items-center p-4 mb-4 text-gray-700 bg-white rounded-lg shadow-lg transform transition-all duration-300`;
            
            // Toast icon based on type
            let icon = '';
            switch (type) {
                case 'success':
                    icon = '<svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>';
                    break;
                case 'error':
                    icon = '<svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>';
                    break;
                case 'warning':
                    icon = '<svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>';
                    break;
                default:
                    icon = '<svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h2a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>';
            }
            
            toast.innerHTML = `
                <div class="flex-shrink-0">${icon}</div>
                <div class="ml-3">
                    ${title ? `<div class="font-medium">${title}</div>` : ''}
                    <div class="text-sm">${message}</div>
                </div>
                <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex h-8 w-8">
                    <span class="sr-only">Close</span>
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            `;
            
            // Add to toast container
            let container = document.querySelector(`.toast-container-${position}`);
            if (!container) {
                container = document.createElement('div');
                container.className = `toast-container toast-container-${position} fixed z-50`;
                
                // Position the container
                switch (position) {
                    case 'top-right':
                        container.className += ' top-4 right-4';
                        break;
                    case 'top-left':
                        container.className += ' top-4 left-4';
                        break;
                    case 'bottom-right':
                        container.className += ' bottom-4 right-4';
                        break;
                    case 'bottom-left':
                        container.className += ' bottom-4 left-4';
                        break;
                    default:
                        container.className += ' top-4 right-4';
                }
                
                document.body.appendChild(container);
            }
            
            container.appendChild(toast);
            
            // Trigger reflow to enable animation
            void toast.offsetWidth;
            toast.classList.add('opacity-100');
            
            // Auto remove after delay
            if (duration > 0) {
                setTimeout(() => {
                    toast.classList.add('opacity-0', 'translate-y-2');
                    setTimeout(() => toast.remove(), 150);
                }, duration);
            }
            
            // Close button
            const closeButton = toast.querySelector('button');
            if (closeButton) {
                closeButton.addEventListener('click', () => {
                    toast.classList.add('opacity-0', 'translate-y-2');
                    setTimeout(() => toast.remove(), 150);
                });
            }
            
            return toast;
        };
    };
    
    // Initialize all components
    const init = () => {
        initTooltips();
        initDropdowns();
        initModals();
        initForms();
        initTabs();
        initAccordions();
        initToasts();
        
        // Add any additional initialization code here
        
        console.log('Smart Agriculture application initialized');
    };
    
    // Initialize when DOM is fully loaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
});

// Helper function to format numbers with commas
function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

// Helper function to debounce function calls
function debounce(func, wait) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

// Helper function to throttle function calls
function throttle(func, limit) {
    let inThrottle;
    return function(...args) {
        if (!inThrottle) {
            func.apply(this, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        formatNumber,
        debounce,
        throttle
    };
}
