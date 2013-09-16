BEM.DOM.decl('mod-shop-edit', {

    onSetMod: {

        js: function() {
            this.bindItemCat();
        }

    },

    /**
     * Сделать все категории в списке родительских категорий невыбранными
     * @param {Array} blocksItemCat Массив блоков категорий
     */
    unselectAllItemCats: function(blocksItemCat) {
        blocksItemCat.forEach(function(itemCat) {
            itemCat.unselect();
        });
    },

    /**
     * Установить событие выбора родительской категории
     */
    bindItemCat: function() {

        var that = this,
            blocksItemCat = this.findBlocksInside('mod-shop-itemcat'),
            hiddenInputParent = this.elem('catparent');

        blocksItemCat && blocksItemCat.forEach(function(itemCat) {
            itemCat.bindCatClick(function(e) {

                var item = $(e.target);

                if(this.hasMod(item, 'selected')) {
                    itemCat.unselect();
                    hiddenInputParent.val('');
                } else {
                    that.unselectAllItemCats(blocksItemCat);
                    itemCat.select();
                    hiddenInputParent.val(item.attr('href'));
                }

                return false;
            });
        });
    },

    /**
     * Показать/скрыть форму выбора родительской категории
     */
    toggleForm: function() {

        var arrow = this.elem('harr');

        this.elem('list', 'editable', 'yes').slideToggle(200, function() {
            arrow.html($(this).is(':hidden') ? '&#9650;' : '&#9660;');
        });
    }

}, {

    live: function() {

        this
            .liveBindTo({ elem: 'link', modName: 'change', modVal: 'parent' }, 'click', function() {
                this.toggleForm();
                return false;
            });

        return false;
    }

});