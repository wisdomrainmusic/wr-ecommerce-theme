(function ($) {

    $(document).ready(function(){

        console.log("HB-ADMIN-JS INITIALIZED");

        const rowsWrapper = document.getElementById("wr-hb-rows");
        const widgetList = document.getElementById("wr-hb-widget-list");

        if (!rowsWrapper || !widgetList) {
            console.warn("HB Admin: Required containers NOT FOUND");
            return;
        }

        // ------------------------------------------------------------
        // 1) WIDGET POOL (Sol panel)
        // ------------------------------------------------------------
        Sortable.create(widgetList, {
            group: {
                name: "hb-widgets",
                pull: "clone",
                put: false
            },
            sort: false,
            animation: 150,
            ghostClass: 'wr-hb-ghost'
        });

        // ------------------------------------------------------------
        // 2) DROPZONE AKTİFLEŞTİR
        // ------------------------------------------------------------
        function activateAllDropzones() {

            document.querySelectorAll(".wr-hb-dropzone").forEach(function (zone) {

                Sortable.create(zone, {
                    group: {
                        name: "hb-widgets",
                        pull: false,
                        put: true
                    },
                    animation: 150,
                    ghostClass: 'wr-hb-ghost',
                    onAdd: function (evt) {
                        let item = evt.item;
                        item.classList.add("wr-hb-widget-added");
                    }
                });

            });

        }

        activateAllDropzones();

        // ------------------------------------------------------------
        // 3) ROW EKLE
        // ------------------------------------------------------------
        $("#wr-hb-add-row").on("click", function(){

            console.log("Add Row clicked");

            let rowHTML = `
            <div class="wr-hb-row">
                <div class="wr-hb-row-header">
                    <strong>ROW</strong>
                    <button type="button" class="wr-hb-remove-row">Remove</button>
                </div>

                <div class="wr-hb-row-inner">
                    <div class="wr-hb-dropzone" data-zone="left"></div>
                    <div class="wr-hb-dropzone" data-zone="center"></div>
                    <div class="wr-hb-dropzone" data-zone="right"></div>
                </div>
            </div>
            `;

            $("#wr-hb-rows").append(rowHTML);

            activateAllDropzones();
        });

        // ------------------------------------------------------------
        // 4) ROW SİL
        // ------------------------------------------------------------
        $(document).on("click", ".wr-hb-remove-row", function () {
            $(this).closest(".wr-hb-row").remove();
        });

    });

})(jQuery);
