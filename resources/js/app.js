import './bootstrap';
import 'leaflet/dist/leaflet.css';
import L from 'leaflet';

import { TanggapToast } from './interactions/toast';
import { TanggapTooltip } from './interactions/tooltip';
import { TanggapModal } from './interactions/modal';
import { TanggapSidepanel } from './interactions/sidepanel';
import { FormFeedback } from './interactions/form-feedback';
import { MapCrossHighlight } from './interactions/map-crosshighlight';

window.L = L;
window.TanggapToast = TanggapToast;
window.TanggapTooltip = TanggapTooltip;
window.TanggapModal = TanggapModal;
window.TanggapSidepanel = TanggapSidepanel;
window.MapCrossHighlight = MapCrossHighlight;

document.addEventListener('DOMContentLoaded', () => {
    TanggapToast.init();
    TanggapTooltip.init();
    TanggapModal.init();
    TanggapSidepanel.init();
    FormFeedback.init();
});
