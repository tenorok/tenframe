BEM.DOM.decl('mod-shop-dropdown', {

    /**
     * Показать выпадающее меню
     * @param {Object} position Позиция
     * @returns {BEM}
     */
    show: function(position) {

        this.setMod('display', 'yes');

        this.domElem.css({
            top: position.top + 29,
            left: position.lef - 8
        });

        this.bindToDoc('click', function(e) {
            if(e.isPropagationStopped()) return;
            this.hide();
        });

        return this;
    },

    /**
     * Скрыть выпадающее меню
     * @returns {BEM}
     */
    hide: function() {
        this.delMod('display');
        this.unbindFromDoc('click');
        return this;
    },

    /**
     * Установить ссылки
     * @param {Stirng} href Базовый адрес
     * @param {Array} elems Массив элементов
     * @returns {BEM}
     */
    setHref: function(href, elems) {

        var block = this;

        elems.forEach(function(elem) {
            block.elem(elem).attr({
                href: href + elem
            });
        });

        return this;
    }

}, {

    live: function() {

        this
            .liveBindTo('click', function(e) {
                e.stopPropagation();
            });

        return false;
    }

});