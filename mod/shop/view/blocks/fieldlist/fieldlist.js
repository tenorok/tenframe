BEM.DOM.decl('mod-shop-fieldlist', {

    onSetMod: {

        js: function() {
            this._fieldHtml = this.elem('item').htmlWithParent();           // Получение html-структуры добавления поля категории
            this._fieldCount = 1;                                           // Количество полей
        }

    },

    /**
     * Добавление чистой формы для ввода информации об ещё одном поле
     */
    addField: function() {
        this.domElem.append(this._fieldHtml.replace(/options_0/g, 'options_' + this._fieldCount++));
    }

});