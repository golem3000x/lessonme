import Shepherd from 'shepherd.js';
import 'shepherd.js/dist/css/shepherd.css';

/**
 * Initialize and configure the interactive tour
 */
export function initTour() {
    const tour = new Shepherd.Tour({
        useModalOverlay: true,
        defaultStepOptions: {
            cancelIcon: {
                enabled: true
            },
            classes: 'shepherd-theme-custom',
            scrollTo: { behavior: 'smooth', block: 'center' },
            arrow: false,
            when: {
                show() {
                    const currentStep = tour.getCurrentStep();
                    const currentStepElement = currentStep.getElement();
                    
                    // Apply custom styles directly
                    if (currentStepElement) {
                        // Style the modal itself
                        currentStepElement.style.cssText = 'z-index: 9999 !important; max-width: 380px !important; border-radius: 14px !important; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important; background: linear-gradient(to bottom, #ffffff, #f8fafc) !important; border: 3px solid transparent !important;';
                        
                        // Style the modal overlay
                        const overlay = document.querySelector('.shepherd-modal-overlay-container');
                        if (overlay) {
                            overlay.style.cssText = 'background: rgba(15, 23, 42, 0.75) !important; backdrop-filter: blur(4px) !important;';
                        }
                        
                        // Style the highlighted target element with rounded corners
                        const target = currentStep.target;
                        if (target && typeof target === 'object') {
                            const targetElement = typeof target === 'string' ? document.querySelector(target) : target;
                            if (targetElement) {
                                targetElement.style.borderRadius = '12px !important';
                                targetElement.style.transition = 'box-shadow 0.3s ease, border-radius 0.3s ease';
                            }
                        }
                        
                        // Also style shepherd-target class
                        const shepherdTarget = document.querySelector('.shepherd-target');
                        if (shepherdTarget) {
                            shepherdTarget.style.borderRadius = '12px !important';
                        }
                        
                        const header = currentStepElement.querySelector('.shepherd-header');
                        const content = currentStepElement.querySelector('.shepherd-content');
                        const footer = currentStepElement.querySelector('.shepherd-footer');
                        const title = currentStepElement.querySelector('.shepherd-title');
                        const text = currentStepElement.querySelector('.shepherd-text');
                        
                        if (header) {
                            header.style.cssText = 'padding: 0.875rem 1.125rem !important; background: white !important; border-bottom: none !important;';
                        }
                        
                        if (title) {
                            title.style.cssText = 'font-size: 1.125rem !important; font-weight: 700 !important; margin-bottom: 0 !important; background: linear-gradient(135deg, #1e40af, #7c3aed, #db2777) !important; -webkit-background-clip: text !important; -webkit-text-fill-color: transparent !important; background-clip: text !important;';
                        }
                        
                        if (text) {
                            text.style.cssText = 'color: #374151 !important; font-size: 0.875rem !important; line-height: 1.4 !important;';
                        }
                        
                        if (content) {
                            content.style.cssText = 'padding: 0.625rem 1.125rem !important; background: white !important;';
                        }
                        
                        if (footer) {
                            footer.style.cssText = 'padding: 0.875rem 1.125rem !important; background: white !important; border-top: none !important; display: flex !important; flex-direction: column !important; gap: 8px !important;';
                            
                            // Wrap buttons in a group
                            const buttons = footer.querySelectorAll('.shepherd-button, .shepherd-button-secondary');
                            if (buttons.length > 0 && !footer.querySelector('.shepherd-button-group')) {
                                const buttonGroup = document.createElement('div');
                                buttonGroup.className = 'shepherd-button-group';
                                buttonGroup.style.cssText = 'display: flex !important; gap: 10px !important; justify-content: flex-end !important;';
                                buttons.forEach(button => {
                                    if (button.classList.contains('shepherd-button-secondary')) {
                                        button.style.cssText = 'padding: 0.625rem 1.25rem !important; border-radius: 10px !important; font-weight: 600 !important; font-size: 0.9375rem !important; background: #f3f4f6 !important; color: #374151 !important; border: 2px solid #d1d5db !important;';
                                    } else {
                                        button.style.cssText = 'padding: 0.625rem 1.25rem !important; border-radius: 10px !important; font-weight: 700 !important; font-size: 0.9375rem !important; background: linear-gradient(135deg, #3b82f6, #6366f1) !important; color: white !important; border: none !important; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;';
                                    }
                                    buttonGroup.appendChild(button);
                                });
                                footer.appendChild(buttonGroup);
                            }
                            
                            // Add progress indicator
                            if (!footer.querySelector('.shepherd-progress')) {
                                const progress = document.createElement('div');
                                progress.className = 'shepherd-progress';
                                progress.style.cssText = 'margin-top: 0.5rem !important; padding-top: 0.5rem !important; font-size: 0.75rem !important; text-align: center !important; font-weight: 600 !important; color: #6b7280 !important; border-top: none !important; display: flex !important; align-items: center !important; justify-content: center !important; gap: 6px !important;';
                                progress.innerHTML = `📍 Step ${tour.steps.indexOf(currentStep) + 1} of ${tour.steps.length}`;
                                footer.insertBefore(progress, footer.firstChild);
                            }
                        }
                    }
                }
            }
        }
    });

    // Step 1: Welcome
    tour.addStep({
        id: 'welcome',
        title: window.tourTranslations?.welcome_title || 'Welcome to LessonMe!',
        text: window.tourTranslations?.welcome_text || 'Let\'s take a quick tour to help you get started with the platform.',
        buttons: [
            {
                text: window.tourTranslations?.skip || 'Skip',
                action: tour.cancel,
                classes: 'shepherd-button-secondary'
            },
            {
                text: window.tourTranslations?.next || 'Next',
                action: tour.next
            }
        ]
    });

    // Step 2: Dashboard
    tour.addStep({
        id: 'dashboard',
        title: window.tourTranslations?.dashboard_title || 'Your Dashboard',
        text: window.tourTranslations?.dashboard_text || 'This is your personal dashboard. Here you can track your progress, see recent scores, and monitor your learning statistics.',
        attachTo: {
            element: '.stats-cards',
            on: 'bottom'
        },
        modalOverlayOpeningPadding: 12,
        popperOptions: {
            modifiers: [
                {
                    name: 'offset',
                    options: {
                        offset: [0, 24]
                    }
                }
            ]
        },
        buttons: [
            {
                text: window.tourTranslations?.back || 'Back',
                action: tour.back,
                classes: 'shepherd-button-secondary'
            },
            {
                text: window.tourTranslations?.next || 'Next',
                action: tour.next
            }
        ]
    });

    // Step 3: Lessons Navigation
    tour.addStep({
        id: 'lessons-nav',
        title: window.tourTranslations?.lessons_title || 'Browse Lessons',
        text: window.tourTranslations?.lessons_text || 'Click here to browse available lessons, create new ones, or view your lesson history.',
        attachTo: {
            element: 'a[href*="lessons"]',
            on: 'bottom'
        },
        modalOverlayOpeningPadding: 12,
        popperOptions: {
            modifiers: [
                {
                    name: 'offset',
                    options: {
                        offset: [0, 24]
                    }
                }
            ]
        },
        buttons: [
            {
                text: window.tourTranslations?.back || 'Back',
                action: tour.back,
                classes: 'shepherd-button-secondary'
            },
            {
                text: window.tourTranslations?.next || 'Next',
                action: tour.next
            }
        ]
    });

    // Step 4: Topics Navigation
    tour.addStep({
        id: 'topics-nav',
        title: window.tourTranslations?.topics_title || 'Manage Topics',
        text: window.tourTranslations?.topics_text || 'Create and customize topics for your lessons. Topics help organize your learning materials.',
        attachTo: {
            element: 'a[href*="topics"]',
            on: 'bottom'
        },
        modalOverlayOpeningPadding: 12,
        popperOptions: {
            modifiers: [
                {
                    name: 'offset',
                    options: {
                        offset: [0, 24]
                    }
                }
            ]
        },
        buttons: [
            {
                text: window.tourTranslations?.back || 'Back',
                action: tour.back,
                classes: 'shepherd-button-secondary'
            },
            {
                text: window.tourTranslations?.next || 'Next',
                action: tour.next
            }
        ]
    });

    // Step 5: Notifications
    tour.addStep({
        id: 'notifications',
        title: window.tourTranslations?.notifications_title || 'Notifications',
        text: window.tourTranslations?.notifications_text || 'Stay updated with real-time notifications about your lessons, assignments, and progress.',
        attachTo: {
            element: '.notifications-panel',
            on: 'bottom-start'
        },
        modalOverlayOpeningPadding: 12,
        popperOptions: {
            modifiers: [
                {
                    name: 'offset',
                    options: {
                        offset: [0, 24]
                    }
                }
            ]
        },
        buttons: [
            {
                text: window.tourTranslations?.back || 'Back',
                action: tour.back,
                classes: 'shepherd-button-secondary'
            },
            {
                text: window.tourTranslations?.next || 'Next',
                action: tour.next
            }
        ]
    });

    // Step 6: Profile
    tour.addStep({
        id: 'profile',
        title: window.tourTranslations?.profile_title || 'Your Profile',
        text: window.tourTranslations?.profile_text || 'Access your profile settings, language preferences, and account options here.',
        attachTo: {
            element: '.user-menu-dropdown',
            on: 'bottom-end'
        },
        modalOverlayOpeningPadding: 12,
        popperOptions: {
            modifiers: [
                {
                    name: 'offset',
                    options: {
                        offset: [0, 24]
                    }
                }
            ]
        },
        buttons: [
            {
                text: window.tourTranslations?.back || 'Back',
                action: tour.back,
                classes: 'shepherd-button-secondary'
            },
            {
                text: window.tourTranslations?.next || 'Next',
                action: tour.next
            }
        ]
    });

    // Step 7: Final
    tour.addStep({
        id: 'complete',
        title: window.tourTranslations?.complete_title || 'You\'re All Set!',
        text: window.tourTranslations?.complete_text || 'You\'re ready to start your learning journey! Visit the Help Center anytime if you need assistance.',
        buttons: [
            {
                text: window.tourTranslations?.finish || 'Get Started',
                action: function() {
                    tour.complete();
                    // Notify server that tour is completed
                    if (window.Livewire) {
                        window.Livewire.dispatch('tour-completed');
                    }
                }
            }
        ]
    });

    return tour;
}

/**
 * Start the tour
 */
export function startTour() {
    const tour = initTour();
    tour.start();
}

/**
 * Restart the tour (for Help Center)
 */
export function restartTour() {
    const tour = initTour();
    tour.start();
}
