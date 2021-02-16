define([], function() {
    var RedirectManager = {};

    RedirectManager.init = function() {
        var self = this;
        self.filterForm = document.getElementById('redirect-manager-filter-form');
        self.sortingSelect = this.filterForm.querySelector("[name=sorting]");

        self.sortingSelect.addEventListener('change', function() {
            self.filterForm.submit();
        });
    };

    RedirectManager.init();


    // To let the module be a dependency of another module, we return our object
    return RedirectManager;
});
