// Контроллер работы с категориями

core.add({

    mod: {

        shop: {

            categories: {

                block: '%mod-shop-categories',
        
                controller: {
                    
                    list: function() {

                        core.mod.shop.categories.list.init();        // Инициализация работы с категориями
                    },

                    add: function() {

                        core.mod.shop.categories.add.init();
                    },

                    edit: function() {

                        core.mod.shop.categories.add.init();
                        core.mod.shop.categories.edit.init();
                    }
                }
            }
        }
    }
});