// Дополнительные функции jquery

/**
 *
 * Аналог html(), но возвращает не только содержимое, а так же и сам тег
 */
$.fn.htmlWithParent = function() { return $("<div/>").append($(this).clone()).html(); };