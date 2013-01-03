// Модель работы со списком категорий

core.add({

    mod: {

        shop: {

            categories: {

                list: {

                    init: function() {                                                                    // Инициализация работы с категориями
                        
                        var that = this;
                        
                        this.block = core.mod.shop.categories.block;
                        
                        $(this.block + '(cat)').click(function() {                                        // Событие клика по категории
                            return that.showDropdown.call(that, this);
                        });

                        $(document).click(function() {                                                    // Событие клика в пустое место
                            that.hideDropdown.call(that);
                        });

                        this.drawHarr();                                                                  // Рисование необходимых выпадушек
                        this.sortable();                                                                  // Инициализация сортировки категорий
                    },

                    showDropdown: function(link) {                                                        // Отображение выпадушки с меню работы над категорией
                        
                        var $link    = $(link),
                            position = $link.position(),
                            top      = position.top,
                            left     = position.left,
                            href     = $link.attr('href'),
                            dropdown = this.block + '(dropdown-menu)';

                        $(this.block + '(dropdown)').css({                                                // Изменение положения выпадушки
                            'display': 'block',
                            'top'    : top  + 29,
                            'left'   : left - 8
                        });
                        
                        $(dropdown + '{add-product}').attr({                                              // Изменение адреса ссылки добавления товара
                            'href': href + 'addproduct'
                        });

                        $(dropdown + '{add-category}').attr({                                             // Изменение адреса ссылки добавления подкатегории
                            'href': href + 'addcategory'
                        });

                        $(dropdown + '{edit}').attr({                                                     // Изменение адреса ссылки изменения категории
                            'href': href
                        });

                        return false;                                                                     // Отмена перехода по ссылке
                    },

                    hideDropdown: function() {                                                            // Скрытие выпадушки с меню работы над категорией
                        
                        $(this.block + '(dropdown)').css({
                            'display': 'none'
                        });
                    },

                    drawHarr: function() {                                                                // Отрисовка сворачивающих стрелочек для вложенных списков категорий

                        var arrow = '<span class="' + this.block.substr(1) + '__harr">&#9660;</span>';    // Шаблон стрелочки (без первого символа процента)

                        $(this.block + '(item):has(' + this.block + '(item))')                            // Если у категории есть подкатегория
                            .children(this.block + '(name)')                                              // то после её имени
                            .append(arrow);                                                               // добавляется стрелочка

                        var that = this;

                        $(this.block + '(harr)').click(function() {                                       // Добавление события клика по сворачивающим стрелочкам
                            that.slideCategoryList.call(that, this);
                        });
                    },

                    slideCategoryList: function(arrow) {                                                  // Скрытие / Отображение вложенных категорий

                        var arrow = $(arrow);                                                             // Объект, по которому был произведён клик

                        arrow
                            .parent()
                            .next(this.block + '(list)')                                                  // Получение объекта списка, который нужно скрыть или отобразить
                            .slideToggle(200, function() {                                                // Скрытие и отображение списка
                                
                                if($(this).is(':hidden'))                                                 // Изменение стрелочки
                                    arrow.html('&#9650;');
                                else
                                    arrow.html('&#9660;');
                            });
                    },

                    sortable: function() {                                                                // Сортировка категорий
                        
                        var that = this;

                        $(this.block + '(list)')
                            .disableSelection()
                            .sortable({
                                start: function(e, ui) {
                                    $(ui.placeholder).hide(300);                                          // Добавление плавной анимации
                                },
                                change: function(e, ui) {
                                    $(ui.placeholder).hide().show(300);                                   // Добавление плавной анимации
                                },
                                tolerance: 'pointer',
                                update: function(data) {
                                    that.serializeList.call(that, data);
                                },
                                handle: this.block + '(draggable)',                                       // Элемент для зацепки
                                axis: 'y',                                                                // Перемещение элементов допускается только по вертикали
                                containment: 'parent'                                                     // Перемещение допускается только в родительском контейнере
                            });
                    },

                    serializeList: function(data) {                                                       // Сохранение новой последовательности категорий

                        var items = $(data.target).children(this.block + '(item)');                       // Все участвующие в сортировке категории

                        var arr = [];                                                                     // Массив для сохранения id категорий

                        $.each(items, function() {                                                        // Цикл по категориям
                            
                            var id =                                                                      // Формирование чистого идентификатора текущей категории
                                $(this)
                                    .attr('id')
                                    .split('_')[1];
                            
                            arr.push(id);                                                                 // Добавление чистого идентификатора в массив
                        });

                        $.ajax({                                                                          // Отправка запроса
                            type: 'GET',
                            url: '/mod/shop/app/ajax/categories.php',
                            data: {
                                event: 'sort',                                                            // на сохранение порядка категорий
                                categories: arr                                                           // с полученным массивом
                            },
                            dataType: 'json'
                        });
                    }
                }
            }
        }
    }
});