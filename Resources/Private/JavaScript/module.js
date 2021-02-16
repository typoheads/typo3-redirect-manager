define([], function() {
    var RedirectManager = {};

    RedirectManager.init = function() {
        var self = this;
        self.filterForm = document.getElementById('redirect-manager-filter-form');
        self.onChangeSelects = this.filterForm.querySelectorAll('[data-on-change="reload"]');

        self.resetButton = this.filterForm.querySelector("[id=reset-form]");

        for(var i=0; i<self.onChangeSelects.length; i++) {
            self.onChangeSelects[i].addEventListener('change', function() {
                self.filterForm.submit();
            });
        }

        self.resetButton.addEventListener('click', function(e) {
            var inputs = self.filterForm.querySelectorAll('input[type="text"]');
            for(var i=0; i<inputs.length; i++) {
                inputs[i].value = '';
            }
            var selects = self.filterForm.querySelectorAll('select');
            for(var i=0; i<selects.length; i++) {
                selects[i].value = '';
                var options = selects[i].querySelectorAll('option');
                for(var j=0; j<options.length; j++) {
                    options[j].selected = false;
                }
            }
            self.filterForm.submit();
            e.preventDefault();
        });
    };

    RedirectManager.init();

    // To let the module be a dependency of another module, we return our object
    return RedirectManager;
});
