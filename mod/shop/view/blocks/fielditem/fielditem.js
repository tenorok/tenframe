BEM.DOM.decl('mod-shop-fielditem', {

    onSetMod: {

        js: function() {
            this._parent = $('.mod-shop-fieldlist').bem('mod-shop-fieldlist');
            this._classifierHtml = this.elem('classifier').htmlWithParent();
            this._fieldAdded = false;
            this._classifierCount = 1;
        }

    },

    /**
     * Добавление чистой формы для ввода информации об ещё одном поле
     */
    addField: function() {
        if(this._fieldAdded) return;
        // Следующее поле ещё не добавлялось
        this._fieldAdded = true;
        this._parent.addField();
    },

    /**
     * Отображение/сокрытие полей для "Выпадающий список"
     * @param {Boolean} isShow Показывать/не показывать
     */
    toggleClassifier: function(isShow) {
        if(isShow) {
            this.setMod('classifier', 'yes');
            this.elem('classifier').focus();
            this.elem('hiddeninput').val(1);
        } else {
            this.delMod('classifier');
            this.elem('hiddeninput').val(0);
        }
    },

    /**
     * Добавление чистого поля для ввода значения выпадающего списка
     * @param {jQuery} elem Поле классификатора, в которое идёт ввод
     */
    addClassifierValue: function(elem) {
        if(this.getMod(elem, 'added')) return;
        // Следующее поле классификатора ещё не добавлялось
        this.setMod(elem, 'added', 'yes');
        this.elem('hiddenitem').append(this._classifierHtml.replace(/Значение 1/, 'Значение ' + ++this._classifierCount));
    }

}, {

    live: function() {

        this
            .liveBindTo('existfield', 'change', function(e) {

                if(e.data.domElem.val() != 'new') {                             // Если выбрана уже существующая категория
                    this.addField();                                            // Нужно добавить ещё одну пустую форму для ввода нового поля
                    this.setMod('value', 'exist');
                } else {                                                        // Иначе выбрано Новое поле
                    this.delMod('value');
                }
            })
            .liveBindTo('textinput selectinput numberinput', 'keyup change', function() {
                this.addField();
            })
            .liveBindTo('checkboxinput', 'change', function(e) {
                this.toggleClassifier(e.data.domElem.is(':checked'));
            })
            .liveBindTo('classifier', 'keyup', function(e) {
                this.addClassifierValue(e.data.domElem);
            });

        return false;
    }

});