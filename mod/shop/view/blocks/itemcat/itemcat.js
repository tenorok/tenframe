BEM.DOM.decl('mod-shop-itemcat', {

    onSetMod: {

        js: function() {
            this._isSubCat = !this.findBlockInside('mod-shop-listcat').domElem.is(':empty');

            var childrens = this.domElem.children();
            this._dom = {
                name: $(childrens[0]),
                listcat: $(childrens[1])
            };
            this._dom.arrow = this.drawHarr();
        }

    },

    /**
     * Проверка на существование вложенных категорий
     * @returns {Boolean}
     */
    isSubCat: function() {
        return this._isSubCat;
    },

    /**
     * Получить блок выпадушки
     * @returns {jQuery}
     */
    getDropdown: function() {
        return this._dropdown || (this._dropdown = this.findBlockOutside('mod-shop-categories').findBlockInside('mod-shop-dropdown'));
    },

    /**
     * Показать выпадающий список рядом с категорией
     * @param {jQuery} link Ссылка категории
     */
    showDropdown: function(link) {
        this.getDropdown()
            .setHref(link.attr('href'), ['addproduct', 'addcategory', 'edit'])
            .show(link.position());
    },

    /**
     * Отрисовка сворачивающих стрелочек для вложенных списков категорий
     * @return {Boolean|jQuery} Добавленная стрелочка
     */
    drawHarr: function() {
        // Если у категории нет подкатегорий
        if(!this.isSubCat()) return false;

        var arrow = this._dom.name.append('<span class="' + this.buildSelector('harr').substr(1) + '">&#9660;</span>');

        return this.findElem(arrow, 'harr');
    },

    /**
     * Свернуть/развернуть подкатегории
     */
    slide: function() {
        var dom = this._dom;
        dom.listcat.slideToggle(200, function() {
            dom.arrow.html(dom.listcat.is(':hidden')? '&#9650;' : '&#9660;');
        });
    },

    /**
     * Подписать событие на элемент cat
     * @param {Function} callback Колбек
     */
    bindCatClick: function(callback) {
        this.elem('cat').eq(0).unbind('click').on('click', callback.bind(this));
    },

    /**
     * Сделать категорию выбранной
     */
    select: function() {
        this.setMod(this.elem('cat').eq(0), 'selected', 'yes');
    },

    /**
     * Сделать категорию невыбранной
     */
    unselect: function() {
        this.delMod(this.elem('cat').eq(0), 'selected');
    }

}, {

    live: function() {

        this
            .liveBindTo('cat', 'click', function(e) {
                e.stopPropagation();
                this.showDropdown(e.data.domElem);
                return false;
            })
            .liveBindTo('harr', 'click', function() {
                this.slide();
            });

        return false;
    }

});