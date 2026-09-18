(function ($) {
    'use strict';

    var insertAudioOptions = {
        srcid: {
            label: 'ID galerije',
            required: true
        }
    };

    $.extend(true, $.trumbowyg, {
        langs: {
            en: {
                insertGallery: 'Dodaj galeriju'
            }
        },
        plugins: {
            insertGallery: {
                init: function (trumbowyg) {
                    var btnDef = {
                        fn: function () {
                            var insertAudioCallback = function (v) {
                                var node = $('<p>[gallery=' + v.srcid + ']</p>')[0];
                                trumbowyg.range.deleteContents();
                                trumbowyg.range.insertNode(node);
                                return true;
                            };

                            trumbowyg.openModalInsert(trumbowyg.lang.insertGallery, insertAudioOptions, insertAudioCallback);
                        }
                    };

                    trumbowyg.addBtnDef('insertAudio', btnDef);
                }
            }
        }
    });
})(jQuery);
