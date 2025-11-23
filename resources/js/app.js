import './bootstrap';
import { startTour, restartTour } from './tour';

// Make tour functions globally available
window.startTour = startTour;
window.restartTour = restartTour;
