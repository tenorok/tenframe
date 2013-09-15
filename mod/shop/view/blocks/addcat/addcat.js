BEM.DOM.decl('mod-shop-addcat', {

    /**
     * Установить алиас имени категории
     * @param {String} text Текст алиаса
     */
    setAlias: function(text) {
        this.elem('catalias').val(
            tenframe.text(text).translitUri()
        );
    }

}, {

    live: function() {

        this
            .liveBindTo('catname', 'keyup', function() {
                this.setAlias(this.elem('catname').val());
            });

        return false;
    }

});