// Version 1.0.0
// From 16.12.2012

/* core.js
    
    Флаг режима разработчика: core.dev

    Использование параметров маршрута:
        function(param1, param2, ..., paramN)
        или
        core.args.param1;

    Для мониторинга использования History API:
        if(Modernizr.history) {

            core.locChange(function() {
                history.pushState(null, null, '/example/url/');
            });
        }

    Сохранение less в css;
        Тегам подключения less нужно добавить атрибут data-file с именем конечного файла:
        <link type="text/css" rel="stylesheet/less" href="/assets/css/main.less" data-file="main.css">
        Может быть несколько тегов с одинаковым data-file, тогда их содержание объединится.

    Добавление объекта в core:
        core.add(
            to,     // Куда добавлять объект. Жёсткое указание (если переменной не существует, то будет ошибка)
            {}      // Обязательный. Объект, который надо добавить
        );

    Добавление маршрутов:
        core.addRoute({
            url: '/url_1/',
            ctrl: 'ctrl_1',
            func: 'method_1'
        }, {
            url: '/url_2/',
            ctrl: 'ctrl_2',
            func: 'method_2'
        });

*/

// Объект ядра
var core = (function($) {

    var that = {},                                                      // Возвращаемый объект

    parseUrl = function(path) {                                         // Метод разбиения пути на массив элементов

        var url = path.split('/');                                      // Разбиение всего адреса в массив

        for(var i = 0; i < url.length; i++) {                           // Цикл по частям адреса для удаления лишних элементов

            if(
                url[i] === '' ||                                        // Удаление пустых элементов
                url[i] == location.protocol ||                          // Удаление протокола
                url[i] == location.hostname                             // Удаление домена
            ) {
                url.splice(i, 1);
                i--;
            }
        }

        return url;
    },

    curLocation,                                                        // Переменная для хранения текущей адресной строки

    request = function() {                                              // Метод маршрутизации

        if(curLocation == location.href)                                // Если новый адрес совпадает с предыдущим
            return;                                                     // то выполнение функции не требуется
        
        var firstCall = (curLocation === undefined) ? true : false;     // Если текущий адрес ещё не определён, значит страница только загрузилась
        
        curLocation = location.href;                                    // Присваивание текущего адреса

        core.args = {};                                                 // Объявление глобального объекта аргументов

        var href = parseUrl(curLocation),                               // Адресная строка
            routes = core.routes.routes;
        
        for(var route = 0; route < routes.length; route++) {            // Цикл по маршрутам

            var url   = routes[route].url,
                ctrl  = routes[route].ctrl,
                func  = routes[route].func,
                call  = routes[route].call  || 'ever',
                rules = routes[route].rules || null,
                pathArr = [];

            if(typeof(url) == 'string')                                 // Если у маршрута один адрес
                    pathArr[0] = parseUrl(url);                         // Путь текущего адреса
            else {                                                      // Иначе передан массив адресов
                for(var p = 0; p < url.length; p++)                     // Цикл по адресам маршрутов
                    pathArr[p] = parseUrl(url[p]);                      // Путь каждого адреса
            }

            nextpath:
            for(var p = 0; p < pathArr.length; p++) {                   // Цикл по путям адресов маршрута

                var path = pathArr[p];

                if(
                    href.length != path.length ||                       // Если количество частей URL и маршрута разные
                    call == 'load' && !firstCall                        // или маршрут нужно вызывать только при загрузке страницы и сейчас не событие загрузки страницы
                )
                    continue nextpath;                                  // то нужно переходить к проверке следующего маршрута
            
                var args = [];                                          // Массив аргументов

                for(var part = 0; part < path.length; part++) {         // Цикл по частям маршрута

                    var arg = /^{(.*)}$/.exec(path[part]);              // Проверка на {переменную} и вычленение её имени

                    if(arg) {                                           // Если часть маршрута является {переменной}
                        
                        var rule;                                       // Переменная под хранение правила для части маршрута

                        if(
                            rules === null ||                           // и если правил у маршрута совсем нет
                            (rule = rules[arg[1]]) === undefined ||     // или нет правила именно для этой переменной
                            rule.test(href[part])                       // или правило есть и оно проходит проверку
                        ) {
                            core.args[arg[1]] = href[part];             // Добавление свойства для глобального объекта аргументов
                            args.push(href[part]);                      // Запись переменной в массив аргументов для дальнейшей передачи в функцию
                        }
                        else {                                          // Иначе переменная не проходит проверку регулярным выражением
                            
                            core.args = {};                             // Нужно очистить объект аргументов
                            continue nextpath;
                        }
                    }
                    else if(href[part] != path[part]) {                 // Иначе часть пути не является {переменной} и если часть URL не совпадает с частью маршрута
                        
                        core.args = {};                                 // Нужно очистить объект аргументов
                        continue nextpath;
                    }
                }
                
                var obj    = eval(ctrl),                                // Преобразование строки с именем контроллера в объект
                    method = (obj) ?                                    // Если объект определён
                        obj[func] :                                     // то нужно взять его метод
                        print('Controller "' + ctrl + '" is undefined', 'error'); // иначе метод будет равен false

                if(method)                                              // Если метод контроллера найден
                    method.apply(obj, args);                            // его надо вызвать в контексте контроллера и передать массив аргументов
                else if(method === undefined)                           // иначе если метод не определён
                    print('Function "' + func + '" of controller "' + ctrl + '" is undefined', 'error');
            }
        }
    },

    monitor = function() {                                              // Метод отслеживания изменений

        if(Modernizr.hashchange)
            $(window).on('hashchange popstate locchange', request);
        else
            setInterval(request, 500);

    },

    saveLess = function() {

        var set = core.routes.settings.saveless || null;

        if(set && core.dev) {                                           // Если существует опция сохранения LESS и включен режим разработчика
            
            var links = $('link[rel="stylesheet/less"]');               // Получение тегов <link>, id которых начинается с 'less'
            
            var css = {},                                               // Объявление переменной для последующей конкатенации стилей
                fileKey,                                                // Ключ для массива с именем файла
                fileHref,                                               // Путь к файлу
                lessId,                                                 // Идентификатор для less-тега
                tag,                                                    // Переменная для хранения текущего less-тега
                lessTags = [];                                          // Массив less-тегов
            
            $.each(links, function(key, value) {                        // Цикл по всем полученным тегам <link>

                fileKey  = $(value).attr('data-file');                  // Имя конечного файла
                fileHref = $(value).attr('href');                       // Путь к файлу для поиска соответствующего <style>

                lessId = fileHref                                       // Искомый id в чистом виде
                    .replace(/\//g, '-')
                    .slice(1, -5);

                css[fileKey] = css[fileKey] || '';                      // Задание начального значения переменной, если её не существует

                tag = $('style#less\\:' + lessId);                      // Текущий less-тег

                lessTags.push(tag)                                      // Добавление текущего less-тега в массив less-тегов

                css[fileKey] += tag.html();                             // Конкатенация стилей
            });
            
            var compress = (set.compress === undefined || set.compress) ? true : false;

            $.post('/sys/ajax/less.php', {                              // Отправка post-запроса на сохранение стилей в отдельный файл
                event:    'save_lesscss',                               // Событие для AJAX-файла
                css:      JSON.stringify(css),                          // Сконкатенированные стили
                path:     set.path,                                     // Путь к выходящему файлу
                compress: compress                                      // Флаг компрессии CSS-кода
            }, function() {
                
                for(var t = 0; t < lessTags.length; t++)
                    lessTags[t].remove();                               // Удаление всех less-тегов со страницы
            });
        }
    },

    print = function(message, type) {                                   // Метод печати сообщений ядра в консоль

        type = type || 'log';                                           // По умолчанию сообщение печатается, как логи

        var prefix = 'Framework:';                                      // Предваряющий сообщения текст

        switch(type) {

            case 'log':
                console.log(prefix, message);
                break;

            case 'error':
                console.error(prefix, message);
                break;

            case 'warn':
                console.warn(prefix, message);
                break;
        }

        return false;
    },

    init = function() {                                                 // Метод инициализации

        $(function() {                                                  // Вызовы после загрузки документа

            saveLess();
            request();
            monitor();
        });

        return that;
    };

    that.locChange = function(func) {                                   // Обёртка для отслеживания изменений адресной строки

        func();
        $(window).trigger('locchange');
    };
    
    that.add = function(to, obj) {                                      // Метод добавления объекта в объект ядра
        
        if(obj === undefined) {                                         // Если передан один параметр
            obj = to;                                                   // Второй аргумент получает значение первого
            to  = this;                                                 // Первый получает текущий контекст (core)
        }
        
        for(var key in obj) {                                           // Цикл по ключам переданного объекта
            
            var crt = create(to, key, obj[key]);                        // Добавление объекта
            
            if(crt[1])                                                  // Если значение было добавлено то можно перейти к следующему ключу объекта,
                continue;                                               // потому что вместе с текущим значением были добавлены и все его дочерние данные
                                                                        // Сюда доходит выполнение, если значение не было добавлено, так как оно уже существует
            if(typeof(crt[0]) == 'object')                              // Если не был добавлен объект, то надо пройтись по его дочерним ключам
                this.add(crt[0], obj[key]);                             // чтобы при необходимости добавить их к текущему объекту
        }

        function create(to, key, val) {                                 // Приватный метод add, непосредственно добавляющий объект
            
            for(var keyObj in to) {                                     // Цикл по ключам целевого объекта
                
                if(key == keyObj) {                                     // Если ключ, который надо добавить, уже существует
                    
                    if(typeof(val) != 'object')                         // И в качестве значения передан не объект
                        print('"' + key + ': ' + val + '" - already exist', 'error');
                    
                    return [to[key], false];                            // Возвращается существующее значение и флаг недобавленности
                }
            }
                                                                        // Если выполнение попало сюда, то ключа ещё не существует
            return [to[key] = val, true];                               // Возвращается созданное значение и флаг добавленности
        }
    };

    that.addRoute = function() {                                        // Добавление маршрутов
        
        for(var route in arguments)
            core.routes.routes.push(arguments[route]);
    };

    return init();

}(jQuery));