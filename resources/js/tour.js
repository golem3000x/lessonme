import Shepherd from 'shepherd.js';
import 'shepherd.js/dist/css/shepherd.css';

/**
 * Initialize and configure the interactive tour
 */
export function initTour() {
    const t = window.tourTranslations || {};
    
    const tour = new Shepherd.Tour({
        useModalOverlay: true,
        defaultStepOptions: {
            cancelIcon: { enabled: true },
            classes: 'shepherd-theme-custom',
            scrollTo: { behavior: 'smooth', block: 'center' },
            arrow: false,
            modalOverlayOpeningPadding: 12,
            popperOptions: {
                modifiers: [{ name: 'offset', options: { offset: [0, 20] } }]
            },
            when: {
                show() {
                    const currentStep = tour.getCurrentStep();
                    const el = currentStep?.getElement();
                    if (!el) return;
                    
                    // Style modal
                    el.style.cssText = 'z-index: 9999 !important; max-width: 400px !important; border-radius: 16px !important; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.3) !important; background: white !important;';
                    
                    // Style overlay
                    const overlay = document.querySelector('.shepherd-modal-overlay-container');
                    if (overlay) overlay.style.cssText = 'background: rgba(15, 23, 42, 0.8) !important; backdrop-filter: blur(4px) !important;';
                    
                    // Style shepherd-target
                    const shepherdTarget = document.querySelector('.shepherd-target');
                    if (shepherdTarget) shepherdTarget.style.borderRadius = '12px';
                    
                    // Style elements
                    const header = el.querySelector('.shepherd-header');
                    const title = el.querySelector('.shepherd-title');
                    const text = el.querySelector('.shepherd-text');
                    const content = el.querySelector('.shepherd-content');
                    const footer = el.querySelector('.shepherd-footer');
                    
                    if (header) header.style.cssText = 'padding: 1rem 1.25rem 0.5rem !important; background: white !important; border: none !important;';
                    if (title) title.style.cssText = 'font-size: 1.25rem !important; font-weight: 700 !important; background: linear-gradient(135deg, #2563eb, #7c3aed) !important; -webkit-background-clip: text !important; -webkit-text-fill-color: transparent !important; background-clip: text !important;';
                    if (text) text.style.cssText = 'color: #4b5563 !important; font-size: 0.9rem !important; line-height: 1.5 !important;';
                    if (content) content.style.cssText = 'padding: 0.5rem 1.25rem !important; background: white !important;';
                    
                    if (footer) {
                        footer.style.cssText = 'padding: 0.75rem 1.25rem 1rem !important; background: white !important; border: none !important; display: flex !important; flex-direction: column !important; gap: 10px !important;';
                        
                        // Group buttons
                        const buttons = footer.querySelectorAll('.shepherd-button, .shepherd-button-secondary');
                        if (buttons.length > 0 && !footer.querySelector('.shepherd-button-group')) {
                            const group = document.createElement('div');
                            group.className = 'shepherd-button-group';
                            group.style.cssText = 'display: flex !important; gap: 10px !important; justify-content: flex-end !important;';
                            buttons.forEach(btn => {
                                btn.style.cssText = btn.classList.contains('shepherd-button-secondary')
                                    ? 'padding: 0.6rem 1.2rem !important; border-radius: 10px !important; font-weight: 600 !important; font-size: 0.875rem !important; background: #f3f4f6 !important; color: #374151 !important; border: 2px solid #e5e7eb !important; cursor: pointer !important;'
                                    : 'padding: 0.6rem 1.2rem !important; border-radius: 10px !important; font-weight: 700 !important; font-size: 0.875rem !important; background: linear-gradient(135deg, #3b82f6, #6366f1) !important; color: white !important; border: none !important; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3) !important; cursor: pointer !important;';
                                group.appendChild(btn);
                            });
                            footer.appendChild(group);
                        }
                        
                        // Progress indicator
                        if (!footer.querySelector('.shepherd-progress')) {
                            const progress = document.createElement('div');
                            progress.className = 'shepherd-progress';
                            progress.style.cssText = 'font-size: 0.75rem !important; text-align: center !important; font-weight: 600 !important; color: #9ca3af !important;';
                            progress.textContent = `${tour.steps.indexOf(currentStep) + 1} / ${tour.steps.length}`;
                            footer.insertBefore(progress, footer.firstChild);
                        }
                    }
                }
            }
        }
    });

    // Step 1: Welcome
    tour.addStep({
        id: 'welcome',
        title: t.welcome_title || '👋 Welcome to LessonMe!',
        text: t.welcome_text || 'Let\'s take a quick tour to help you get started. We\'ll show you all the key features of the platform.',
        buttons: [
            { text: t.skip || 'Skip Tour', action: tour.cancel, classes: 'shepherd-button-secondary' },
            { text: t.next || 'Let\'s Go! →', action: tour.next }
        ]
    });

    // Step 2: Dashboard Navigation
    tour.addStep({
        id: 'dashboard-nav',
        title: t.dashboard_nav_title || '📊 Dashboard',
        text: t.dashboard_nav_text || 'Your personal command center. View your learning statistics, recent scores, and track your progress here.',
        attachTo: { element: 'a[href="/dashboard"]', on: 'bottom' },
        buttons: [
            { text: t.back || '← Back', action: tour.back, classes: 'shepherd-button-secondary' },
            { text: t.next || 'Next →', action: tour.next }
        ]
    });

    // Step 3: Education Menu
    tour.addStep({
        id: 'education-menu',
        title: t.education_title || '📚 Education',
        text: t.education_text || 'Access all your learning materials here: <br><br>• <b>Lessons</b> — Browse, create, or take AI-generated lessons<br>• <b>Topics</b> — Manage topics and keywords for lessons<br>• <b>Courses</b> — Organized lesson collections',
        attachTo: { element: 'nav .relative:first-of-type button', on: 'bottom' },
        buttons: [
            { text: t.back || '← Back', action: tour.back, classes: 'shepherd-button-secondary' },
            { text: t.next || 'Next →', action: tour.next }
        ]
    });

    // Step 4: Community Menu
    tour.addStep({
        id: 'community-menu',
        title: t.community_title || '👥 Community',
        text: t.community_text || 'Connect with others on the platform:<br><br>• <b>Users</b> — Find teachers or students<br>• <b>Messages</b> — Direct messaging with your connections',
        attachTo: { element: 'nav .relative:nth-of-type(2) button', on: 'bottom' },
        buttons: [
            { text: t.back || '← Back', action: tour.back, classes: 'shepherd-button-secondary' },
            { text: t.next || 'Next →', action: tour.next }
        ]
    });

    // Step 5: Help Center
    tour.addStep({
        id: 'help-center',
        title: t.help_title || '❓ Help Center',
        text: t.help_text || 'Need assistance? Find guides, tutorials, and answers to frequently asked questions here.',
        attachTo: { element: 'a[href*="help"]', on: 'bottom' },
        buttons: [
            { text: t.back || '← Back', action: tour.back, classes: 'shepherd-button-secondary' },
            { text: t.next || 'Next →', action: tour.next }
        ]
    });

    // Step 6: Notifications
    tour.addStep({
        id: 'notifications',
        title: t.notifications_title || '🔔 Notifications',
        text: t.notifications_text || 'Stay informed! Get real-time updates about new assignments, lesson completions, teacher invitations, and more.',
        attachTo: { element: '.notifications-panel', on: 'bottom-start' },
        buttons: [
            { text: t.back || '← Back', action: tour.back, classes: 'shepherd-button-secondary' },
            { text: t.next || 'Next →', action: tour.next }
        ]
    });

    // Step 7: User Profile
    tour.addStep({
        id: 'user-profile',
        title: t.profile_title || '👤 Your Profile',
        text: t.profile_text || 'Manage your account here: view your profile, update settings, change language preferences, or log out.',
        attachTo: { element: '.user-menu-dropdown', on: 'bottom-end' },
        buttons: [
            { text: t.back || '← Back', action: tour.back, classes: 'shepherd-button-secondary' },
            { text: t.next || 'Next →', action: tour.next }
        ]
    });

    // Step 8: Complete
    tour.addStep({
        id: 'complete',
        title: t.complete_title || '🎉 You\'re All Set!',
        text: t.complete_text || 'You\'re ready to start your learning journey!<br><br>Visit the <b>Help Center</b> anytime if you need assistance. Good luck!',
        buttons: [
            { text: t.finish || '🚀 Get Started!', action: () => {
                tour.complete();
                if (window.Livewire) window.Livewire.dispatch('tour-completed');
            }}
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
