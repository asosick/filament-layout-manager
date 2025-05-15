(function(window, document) {
    'use strict';

    // Import Sortable from sortablejs
    const Sortable = window.Sortable || require('sortablejs');

    // Module-specific variables with a unique namespace
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
            const grid = document.querySelector('[x-ref="grid"]');
            if (grid) {
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

    // Initialize on load
    CustomSortableModule.initialize();

    // Reinitialize whenever Livewire re-renders
    document.addEventListener('livewire:update', function() {
        if (window.Livewire && window.Livewire.getByName('editMode')) {
            CustomSortableModule.initialize();
        } else {
            CustomSortableModule.destroy();
        }
    });

    // Expose the module to the global scope if needed
    window.CustomSortableModule = {
        initialize: CustomSortableModule.initialize.bind(CustomSortableModule),
        destroy: CustomSortableModule.destroy.bind(CustomSortableModule)
    };

})(window, document);
