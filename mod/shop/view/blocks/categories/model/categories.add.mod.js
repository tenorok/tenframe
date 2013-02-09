// Модель работы с категориями

core.add({

    mod: {

        shop: {

            categories: {

                add: {

                    fieldHtml: null,                                                                // Переменная для хранения html-структуры добавления поля категории

                    init: function() {                                                              // Инициализация добавления категории

                        var that = this;
                        
                        this.block = core.mod.shop.categories.block;

                        this.fieldHtml = $(this.block + '(fielditem)').htmlWithParent();            // Получение html-структуры добавления поля категории
                        
                        $(this.block + '(catname)').keyup(function() {                              // Событие ввода названия категории
                            that.setAlias.call(that, this);
                        });
                        
                        $(this.block + '(fielditem)')                                               // Событие изменения значения выпадающего списка "Новое поле" и выбора уже существующих
                            .find(this.block + '(selectinput){existfield}')
                            .change(function() {
                                that.changeExistList.call(that, this);
                            });

                        $(this.block + '(textinput){name}').keyup(function() {                      // Событие ввода названия поля
                            that.addField.call(that, this);
                        });

                        $(this.block + '(checkboxinput)').live('change', function() {               // Событие изменения флажка "Выпадающий список"
                            that.changeClassifier.call(that, this);
                        });

                        $(this.block + '(textinput){right-column}')                                 // Событие ввода значения выпадающего списка
                            .live('keyup', function() {
                                that.addClassifierValue.call(that, this);
                            });
                    },

                    setAlias: function(input) {                                                     // Генерация алиаса названия категории на транслите

                        $(this.block + '(catalias)')
                            .val(core.text($(input).val()).translitUri());
                    },

                    changeExistList: function(select) {                                             // Демонстрация и скрытие полей для ввода при изменении выпадающего списка "Новое поле" и выбора уже существующих полей

                        var $select = $(select),
                            labels =                                                                // Получение всех полей для ввода у данного поля
                                $select
                                    .parents(this.block + '(fielditem)')
                                    .find(this.block + '(labelitem)')
                                    .not(this.block + '(existlist)');

                        if($select.val() != 'new') {                                                // Если выбрана уже существующая категория
                            
                            this.addField.call(this, select);                                       // Нужно добавить ещё одну пустую форму для ввода нового поля

                            labels.css({'display': 'none'});                                        // Скрыть все поля для ввода информации о текущем поле
                        }
                        else                                                                        // Иначе выбрано Новое поле
                            labels.css({'display': 'block'});                                       // Нужно показать все поля для ввода информации о текущем поле
                    },

                    addField: function(field) {                                                     // Добавление чистой формы для ввода информации об ещё одном поле

                        if($(field).bemGetMod('field'))                                             // Если по текущему элементу уже добавлялась новая форма
                            return;                                                                 // то больше этого делать не нужно

                        var lastField = $(this.block + '(fielditem)').last();                       // Получение последней формы ввода информации о поле

                        lastField                                                                   // Назначение класса, символизирующего, что данная форма уже добавила после себя новую форму
                            .find(this.block + '(selectinput){existfield}')
                            .bemSetMod('field', 'added');
                        
                        lastField                                                                   // Назначение класса, символизирующего, что данная форма уже добавила после себя новую форму
                            .find(this.block + '(textinput){name}')
                            .bemSetMod('field', 'added');

                        var fieldsCount =                                                           // Количество добавленных полей
                            $(this.block + '(fieldlist)')
                                .children(this.block + '(fielditem)')
                                .length,

                            newFieldHtml =                                                          // Изменение имён полей для ввода значений выпадающего списка
                                this.fieldHtml.replace(/options_0/g, 'options_' + fieldsCount),

                            newField = $(newFieldHtml).appendTo(this.block + '(fieldlist)'),        // Добавление новой формы

                            that = this;
                        
                        $(newField)                                                                 // Назначение события изменения выпадающего списка "Новое поле" и выбора уже существующих полей в новой форме
                            .find(this.block + '(selectinput){existfield}')
                            .change(function() {
                                that.changeExistList.call(that, this);
                            });

                        $(newField)                                                                 // Назначение события ввода названия поля в новой форме
                            .find(this.block + '(textinput){name}')
                            .keyup(function() {
                                that.addField.call(that, this);
                            });
                    },

                    changeClassifier: function(checkbox) {                                          // Обработка изменения флажка "Выпадающий список"

                        var $checkbox = $(checkbox),
                            classifier =                                                            // Получение скрытого элемента списка с полями для ввода значений выпадающего списка
                                $checkbox
                                    .parents(this.block + '(labellist)')
                                    .children(this.block + '(hiddenitem)');

                        var hiddenInput = $checkbox.next();                                         // Получение скрытого поля-флага, говорящего, является ли поле выпадающим списком

                        if($checkbox.is(':checked')) {                                              // Если флажок поставлен
                            
                            $(classifier).css({'display': 'block'});                                // Нужно показать поля для ввода значений выпадающего списка

                            $(classifier)                                                           // Поставить фокус в первое поле
                                .children(this.block + '(textinput)')
                                .focus();

                            $(hiddenInput).attr('value', '1');                                      // Изменение флага, поле является выпадающим списком
                        }
                        else {                                                                      // Иначе флажок не поставлен

                            $(classifier).css({'display': 'none'});                                 // И нужно скрыть поля для ввода значений выпадающего списка

                            $(hiddenInput).attr('value', '0');                                      // Изменение флага, поле не является выпадающим списком
                        }
                    },

                    addClassifierValue: function(field) {                                           // Добавление чистого поля для ввода значения выпадающего списка

                        var $field = $(field);
                        
                        if($field.bemGetMod('added'))                                               // Если по текущему элементу уже добавлялось новое поле
                            return;

                        var parent = $field.parent(this.block + '(labelitem)'),                     // Получение родительского тега

                            valCount =                                                              // Количество полей для ввода значений выпадающего списка
                                parent
                                    .children(this.block + '(textinput)')
                                    .length,

                            newValueHtml =                                                          // Получение html-структуры нового поля для ввода значения выпадающего списка
                                $($field.htmlWithParent())
                                    .attr('placeholder', 'Значение ' + (++valCount)),

                            newValue = newValueHtml.appendTo(parent),                               // Добавление нового поля

                            that = this;

                        $field.bemSetMod('added', 'yes');                                           // Назначение класса, символизирующего, что данное поле уже добавляло после себя новое поле

                        $(newValue).keyup(function() {                                              // Назначение события ввода значения в только что добавленное поле
                                that.addClassifierValue.call(that, this);
                            });
                    }
                }
            }
        }
    }
});