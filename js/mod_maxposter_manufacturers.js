(function() {
window.addEvent('domready', function() {
    $$('.mod-maxposter-manufacturers-list > ul > li').each(function(el){
        var toggler = new Element('a', { 'href' : '#', 'class' : 'toggler', 'styles' : {  }, 'html' : '&nbsp;' });
        toggler.inject(el, 'top');
    });

    $$('.mod-maxposter-manufacturers-list > ul > li > a.toggler').each(function(el){
        var state = false;
        el.addEvent('click', function(e){
            e.stop();
            state = !state;
            this.getParent().getElement('ul').slide(state ? 'in' : 'out');
        });
        if (!el.getParent().hasClass('current')) {
            el.getParent().getElement('ul').slide('out');
            state = false;
        } else {
            state = true;
        }
    });
});})(document.id);
