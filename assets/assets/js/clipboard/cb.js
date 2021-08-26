var common = window.common || {};
common.clipboard = function (selector) {
    var clipboard = new ClipboardJS(selector);
    clipboard.on('success', function (e) {
        common.notify('Скопировано <strong>'+e.text+'</strong>', 'success');
    });
};
