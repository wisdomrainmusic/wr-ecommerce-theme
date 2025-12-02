(function ($) {

    console.log("HB-ADMIN-JS LOADED");

    // Row container
    const rowsWrapper = document.getElementById("wr-hb-rows");
    const widgetList = document.getElementById("wr-hb-widget-list");

    if (!rowsWrapper || !widgetList) {
        console.warn("HB Admin: Required containers NOT FOUND");
        return;
    }

    // ------------------------------------------------------------
    // 1) WIDGET POOL (Sol taraftaki widget listesi)
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
    // 2) VAR OLAN HER SATIRDAKİ DROPZONELARI AKTİF ET
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
    // 3) YENİ ROW EKLE
    // ------------------------------------------------------------
    $("#wr-hb-add-row").on("click", function () {

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

        // Yeni dropzone'ları aktif et
        activateAllDropzones();
    });

    // ------------------------------------------------------------
    // 4) ROW SİLME
    // ------------------------------------------------------------
    $(document).on("click", ".wr-hb-remove-row", function () {
        $(this).closest(".wr-hb-row").remove();
    });

})(jQuery);
