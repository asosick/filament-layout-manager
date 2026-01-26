import Sortable from 'sortablejs';
const CustomSortableModule = {
    instance: null,

    moveEndMorphMarker: function(el) {
        const endMorphMarker = Array.from(el.childNodes).filter((childNode) => {
            return childNode.nodeType === 8 && ['[if ENDBLOCK]><![endif]', '__ENDBLOCK__'].includes(childNode.nodeValue?.trim());
        })[0];

        if (endMorphMarker) {
            el.appendChild(endMorphMarker);
        }
    },

    initialize: function() {
        // Use a more specific selector to avoid conflicts
        const grid = document.querySelector('[x-ref="grid"]');

        if (!grid) return;

        if (this.instance) {
            this.instance.destroy();
        }
        if (grid && !this.instance) {
            this.instance = new Sortable(grid, {
                animation: 150,
                handle: '.handle',
                ghostClass: 'opacity-50',
                onEnd: (evt) => {
                    const orderedIds = Array.from(grid.children).map(el => el.dataset.id);
                    this.moveEndMorphMarker(grid);
                    if (window.Livewire) {
                        window.Livewire.dispatch('updateLayout', { orderedIds: orderedIds });
                    }
                }
            });
        }
    },

    destroy: function() {
        if (this.instance) {
            this.instance.destroy();
            this.instance = null;
        }
    }
};

document.addEventListener('livewire:navigated', () => CustomSortableModule.initialize());
document.addEventListener('livewire:init', () => CustomSortableModule.initialize());

window.CustomSortableModule = CustomSortableModule;
CustomSortableModule.initialize();
