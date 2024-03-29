/*!
 * Nestable jQuery Plugin - Copyright (c) 2012 David Bushell - http://dbushell.com/
 * Dual-licensed under the BSD or MIT licenses
 */
;
(function ($, window, document, undefined) {
    var hasTouch = 'ontouchstart' in window;

    /**
     * Detect CSS pointer-events property
     * events are normally disabled on the dragging element to avoid conflicts
     * https://github.com/ausi/Feature-detection-technique-for-pointer-events/blob/master/modernizr-pointerevents.js
     */
    var hasPointerEvents = (function () {
        var el = document.createElement('div'),
            docEl = document.documentElement;
        if (!('pointerEvents' in el.style)) {
            return false;
        }
        el.style.pointerEvents = 'auto';
        el.style.pointerEvents = 'x';
        docEl.appendChild(el);
        var supports = window.getComputedStyle && window.getComputedStyle(el, '').pointerEvents === 'auto';
        docEl.removeChild(el);
        return !!supports;
    })();

    var eStart = hasTouch ? 'touchstart' : 'mousedown',
        eMove = hasTouch ? 'touchmove' : 'mousemove',
        eEnd = hasTouch ? 'touchend' : 'mouseup',
        eCancel = hasTouch ? 'touchcancel' : 'mouseup';

    var defaults = {
        listNodeName: 'ol',
        itemNodeName: 'li',
        rootClass: 'dd-nestable',
        contentClass: 'dd-content',
        editPanelClass: 'dd-edit-panel',
        listClass: 'dd-list',
        itemClass: 'dd-item',
        dragClass: 'dd-dragel',
        inputOpenClass: 'dd-open',
        handleClass: 'dd-handle',
        collapsedClass: 'dd-collapsed',
        placeClass: 'dd-placeholder',
        inputNameClass: 'dd-input-name',
        noDragClass: 'dd-nodrag',
        emptyClass: 'dd-empty',
        btnGroupClass: 'btn-group',
        expandBtnHTML: '<button data-action="expand" class="dd-button" type="button"></button>',
        collapseBtnHTML: '<button data-action="collapse" class="dd-button" type="button"></button>',
        group: 0,
        maxDepth: 5,
        threshold: 20,
        moveUrl: '',
        createUrl: '',
        updateUrl: '',
        deleteUrl: '',
        namePlaceholder: '',
        deleteAlert: 'The nobe will be removed together with the children. Are you sure?',
        newNodeTitle: 'Enter the new node name'
    };

    function Plugin(element, options) {
        this.w = $(window);
        this.el = $(element);
        this.options = $.extend({}, defaults, options);
        this.init();
    }

    Plugin.prototype = {

        init: function () {
            var tree = this;

            tree.reset();

            tree.el.data('nestable-group', this.options.group);

            tree.placeEl = $('<div class="' + tree.options.placeClass + '"/>');

            $.each(this.el.find(tree.options.itemNodeName), function (k, el) {
                // Вставляем иконки открытия\закрытия дочек
                tree.setParent($(el));
            });

            // Вешаем эвенты клика для открытия панели редактирования
            tree.setPanelEvents(tree.el);
            tree.setActionButtonsEvents(tree.el);

            tree.el.on('click', 'button', function (e) {
                if (tree.dragEl || (!hasTouch && e.button !== 0)) {
                    return;
                }
                var target = $(e.currentTarget),
                    action = target.data('action'),
                    item = target.parent(tree.options.itemNodeName);
                if (action === 'collapse') {
                    tree.collapseItem(item);
                }
                if (action === 'expand') {
                    tree.expandItem(item);
                }
            });

            var onStartEvent = function (e) {
                var handle = $(e.target);
                if (!handle.hasClass(tree.options.handleClass)) {
                    if (handle.closest('.' + tree.options.noDragClass).length) {
                        return;
                    }
                    handle = handle.closest('.' + tree.options.handleClass);
                }
                if (!handle.length || tree.dragEl || (!hasTouch && e.button !== 0) || (hasTouch && e.touches.length !== 1)) {
                    return;
                }
                e.preventDefault();
                tree.dragStart(hasTouch ? e.touches[0] : e);
            };

            var onMoveEvent = function (e) {
                if (tree.dragEl) {
                    e.preventDefault();
                    tree.dragMove(hasTouch ? e.touches[0] : e);
                }
            };

            var onEndEvent = function (e) {
                if (tree.dragEl) {
                    e.preventDefault();
                    tree.dragStop(hasTouch ? e.touches[0] : e);
                }
            };

            if (hasTouch) {
                tree.el[0].addEventListener(eStart, onStartEvent, false);
                window.addEventListener(eMove, onMoveEvent, false);
                window.addEventListener(eEnd, onEndEvent, false);
                window.addEventListener(eCancel, onEndEvent, false);
            } else {
                tree.el.on(eStart, onStartEvent);
                tree.w.on(eMove, onMoveEvent);
                tree.w.on(eEnd, onEndEvent);
            }
        },

        /**
         * Вешаем onClick на тело пункта
         * @returns {*}
         */
        setPanelEvents: function (el) {
            var tree = this;

            el.on('keyup', '.' + tree.options.inputNameClass, function (e) {
                var target = $(e.target),
                    li = target.closest('.' + tree.options.itemClass),
                    content = li.children('.' + tree.options.contentClass);

                content.html(target.val());
            });

            el.on('click', '.' + tree.options.contentClass, function (e) {
                var owner = $(e.target).parent();
                var editPanel = owner.children('.' + tree.options.editPanelClass);

                if (!editPanel.hasClass(tree.options.inputOpenClass)) {
                    $('.' + tree.options.editPanelClass)
                        .slideUp(100)
                        .removeClass(tree.options.inputOpenClass);

                    editPanel.addClass(tree.options.inputOpenClass);
                    editPanel.slideDown(100);
                }
                else {
                    editPanel.removeClass(tree.options.inputOpenClass);
                    editPanel.slideUp(100);
                }
            });
        },

        /**
         * Вешаем onClick на кнопки управления нодой
         * @returns {*}
         */
        setActionButtonsEvents: function (el) {
            var tree = this;

            el.on('click', '.' + tree.options.btnGroupClass + ' [data-action="save"]', function (e) {
                var target = $(e.target),
                    li = target.closest('.' + tree.options.itemClass);

                tree.updateNodeRequest(li);
            });

            el.on('click', '.' + tree.options.btnGroupClass + ' [data-action="delete"]', function (e) {
                var target = $(e.target),
                    li = target.closest('.' + tree.options.itemClass);

                if (confirm(tree.options.deleteAlert)) {
                    tree.deleteNodeRequest(li);
                }
            });
        },

        reset: function () {
            this.mouse = {
                offsetX: 0,
                offsetY: 0,
                startX: 0,
                startY: 0,
                lastX: 0,
                lastY: 0,
                nowX: 0,
                nowY: 0,
                distX: 0,
                distY: 0,
                dirAx: 0,
                dirX: 0,
                dirY: 0,
                lastDirX: 0,
                lastDirY: 0,
                distAxX: 0,
                distAxY: 0
            };
            this.moving = false;
            this.dragEl = null;
            this.dragRootEl = null;
            this.dragDepth = 0;
            this.hasNewRoot = false;
            this.pointEl = null;
        },

        expandItem: function (li) {
            li.removeClass(this.options.collapsedClass);
            li.children('[data-action="expand"]').hide();
            li.children('[data-action="collapse"]').show();
            li.children(this.options.listNodeName).show();
        },

        collapseItem: function (li) {
            var lists = li.children(this.options.listNodeName);
            if (lists.length) {
                li.addClass(this.options.collapsedClass);
                li.children('[data-action="collapse"]').hide();
                li.children('[data-action="expand"]').show();
                li.children(this.options.listNodeName).hide();
            }
        },

        expandAll: function () {
            var tree = this;
            tree.el.find(tree.options.itemNodeName).each(function () {
                tree.expandItem($(this));
            });
        },

        collapseAll: function () {
            var tree = this;
            tree.el.find(tree.options.itemNodeName).each(function () {
                tree.collapseItem($(this));
            });
        },

        setParent: function (li) {
            if (li.children(this.options.listNodeName).length) {
                li.prepend($(this.options.expandBtnHTML));
                li.prepend($(this.options.collapseBtnHTML));
            }
            li.children('[data-action="expand"]').hide();
        },

        unsetParent: function (li) {
            li.removeClass(this.options.collapsedClass);
            li.children('[data-action]').remove();
            li.children(this.options.listNodeName).remove();
        },

        dragStart: function (e) {
            var mouse = this.mouse,
                target = $(e.target),
                dragItem = target.closest(this.options.itemNodeName);

            this.placeEl.css('height', dragItem.height());

            mouse.offsetX = e.offsetX !== undefined ? e.offsetX : e.pageX - target.offset().left;
            mouse.offsetY = e.offsetY !== undefined ? e.offsetY : e.pageY - target.offset().top;
            mouse.startX = mouse.lastX = e.pageX;
            mouse.startY = mouse.lastY = e.pageY;

            this.dragRootEl = this.el;

            this.dragEl = $(document.createElement(this.options.listNodeName)).addClass(this.options.listClass + ' ' + this.options.dragClass);
            this.dragEl.css('width', dragItem.width());

            // fix for zepto.js
            //dragItem.after(this.placeEl).detach().appendTo(this.dragEl);
            dragItem.after(this.placeEl);
            dragItem[0].parentNode.removeChild(dragItem[0]);
            dragItem.appendTo(this.dragEl);

            $(document.body).append(this.dragEl);
            this.dragEl.css({
                'left': e.pageX - mouse.offsetX,
                'top': e.pageY - mouse.offsetY
            });
            // total depth of dragging item
            var i, depth,
                items = this.dragEl.find(this.options.itemNodeName);
            for (i = 0; i < items.length; i++) {
                depth = $(items[i]).parents(this.options.listNodeName).length;
                if (depth > this.dragDepth) {
                    this.dragDepth = depth;
                }
            }
        },

        dragStop: function (e) {
            // fix for zepto.js
            //this.placeEl.replaceWith(this.dragEl.children(this.options.itemNodeName + ':first').detach());
            var el = this.dragEl.children(this.options.itemNodeName).first();
            el[0].parentNode.removeChild(el[0]);
            this.placeEl.replaceWith(el);

            this.dragEl.remove();

            this.moveNodeRequest(el);

            this.el.trigger('change');
            if (this.hasNewRoot) {
                this.dragRootEl.trigger('change');
            }
            this.reset();
        },

        /**
         * Создание нового пункта
         */
        createNode: function () {
            var tree = this,
                wId = tree.el.attr('id');

            var name = prompt(tree.options.newNodeTitle);
            if (name != null) {
                $.ajax({
                    url: this.options.createUrl,
                    method: 'POST',
                    context: document.body,
                    data: {name: name}
                }).success(function (data, textStatus, jqXHR) {
                    $.pjax.reload({container: '#' + wId + '-pjax'});
                }).fail(function (jqXHR) {
                    alert(jqXHR.responseText);
                });
            }
        },

        /**
         * Save new node position on server
         * @param el
         */
        moveNodeRequest: function (el) {
            var id = el.data('id');
            if (typeof id === "undefined" || !id) {
                return false;
            }

            var prev = el.prev(this.options.itemNodeName);
            var next = el.next(this.options.itemNodeName);
            var parent = el.parents(this.options.itemNodeName);

            $.ajax({
                url: this.options.moveUrl + '?id=' + id,
                method: 'POST',
                context: document.body,
                data: {
                    parent: $(parent).data('id'),
                    left: (prev.length ? prev.data('id') : 0),
                    right: (next.length ? next.data('id') : 0)
                }
            }).fail(function (jqXHR) {
                alert(jqXHR.responseText);
            });
        },

        deleteNodeRequest: function (el) {
            var id = el.data('id');
            if (typeof id === "undefined" || !id) {
                return false;
            }

            $.ajax({
                url: this.options.deleteUrl + '?id=' + id,
                method: 'POST',
                context: document.body,
            }).success(function (data, textStatus, jqXHR) {
                el.remove();
            }).fail(function (jqXHR) {
                alert(jqXHR.responseText);
            });
        },

        updateNodeRequest: function (el) {
            var tree = this,
                id = el.data('id');

            if (typeof id === "undefined" || !id) {
                return false;
            }

            var name = el.find('.' + this.options.inputNameClass);

            $.ajax({
                url: this.options.updateUrl + '?id=' + id,
                method: 'POST',
                context: document.body,
                data: {
                    name: name.val()
                }
            }).success(function (data, textStatus, jqXHR) {
                var editPanel = el.children('.' + tree.options.editPanelClass);
                editPanel.removeClass(tree.options.inputOpenClass);
                editPanel.slideUp(100);
            }).fail(function (jqXHR) {
                alert(jqXHR.responseText);
            });
        },

        dragMove: function (e) {
            var tree, parent, prev, next, depth,
                opt = this.options,
                mouse = this.mouse;

            this.dragEl.css({
                'left': e.pageX - mouse.offsetX,
                'top': e.pageY - mouse.offsetY
            });

            // mouse position last events
            mouse.lastX = mouse.nowX;
            mouse.lastY = mouse.nowY;
            // mouse position this events
            mouse.nowX = e.pageX;
            mouse.nowY = e.pageY;
            // distance mouse moved between events
            mouse.distX = mouse.nowX - mouse.lastX;
            mouse.distY = mouse.nowY - mouse.lastY;
            // direction mouse was moving
            mouse.lastDirX = mouse.dirX;
            mouse.lastDirY = mouse.dirY;
            // direction mouse is now moving (on both axis)
            mouse.dirX = mouse.distX === 0 ? 0 : mouse.distX > 0 ? 1 : -1;
            mouse.dirY = mouse.distY === 0 ? 0 : mouse.distY > 0 ? 1 : -1;
            // axis mouse is now moving on
            var newAx = Math.abs(mouse.distX) > Math.abs(mouse.distY) ? 1 : 0;

            // do nothing on first move
            if (!mouse.moving) {
                mouse.dirAx = newAx;
                mouse.moving = true;
                return;
            }

            // calc distance moved on this axis (and direction)
            if (mouse.dirAx !== newAx) {
                mouse.distAxX = 0;
                mouse.distAxY = 0;
            } else {
                mouse.distAxX += Math.abs(mouse.distX);
                if (mouse.dirX !== 0 && mouse.dirX !== mouse.lastDirX) {
                    mouse.distAxX = 0;
                }
                mouse.distAxY += Math.abs(mouse.distY);
                if (mouse.dirY !== 0 && mouse.dirY !== mouse.lastDirY) {
                    mouse.distAxY = 0;
                }
            }
            mouse.dirAx = newAx;

            /**
             * move horizontal
             */
            if (mouse.dirAx && mouse.distAxX >= opt.threshold) {
                // reset move distance on x-axis for new phase
                mouse.distAxX = 0;
                prev = this.placeEl.prev(opt.itemNodeName);
                // increase horizontal level if previous sibling exists and is not collapsed
                if (mouse.distX > 0 && prev.length && !prev.hasClass(opt.collapsedClass)) {
                    // cannot increase level when item above is collapsed
                    tree = prev.find(opt.listNodeName).last();
                    // check if depth limit has reached
                    depth = this.placeEl.parents(opt.listNodeName).length;
                    if (depth + this.dragDepth <= opt.maxDepth) {
                        // create new sub-level if one doesn't exist
                        if (!tree.length) {
                            tree = $('<' + opt.listNodeName + '/>').addClass(opt.listClass);
                            tree.append(this.placeEl);
                            prev.append(tree);
                            this.setParent(prev);
                        } else {
                            // else append to next level up
                            tree = prev.children(opt.listNodeName).last();
                            tree.append(this.placeEl);
                        }
                    }
                }
                // decrease horizontal level
                if (mouse.distX < 0) {
                    // we can't decrease a level if an item preceeds the current one
                    next = this.placeEl.next(opt.itemNodeName);
                    if (!next.length) {
                        parent = this.placeEl.parent();
                        this.placeEl.closest(opt.itemNodeName).after(this.placeEl);
                        if (!parent.children().length) {
                            this.unsetParent(parent.parent());
                        }
                    }
                }
            }

            var isEmpty = false;

            // find list item under cursor
            if (!hasPointerEvents) {
                this.dragEl[0].style.visibility = 'hidden';
            }
            this.pointEl = $(document.elementFromPoint(e.pageX - document.body.scrollLeft, e.pageY - (window.pageYOffset || document.documentElement.scrollTop)));
            if (!hasPointerEvents) {
                this.dragEl[0].style.visibility = 'visible';
            }
            if (this.pointEl.hasClass(opt.handleClass)) {
                this.pointEl = this.pointEl.parent(opt.itemNodeName);
            }
            if (this.pointEl.hasClass(opt.emptyClass)) {
                isEmpty = true;
            }
            else if (!this.pointEl.length || !this.pointEl.hasClass(opt.itemClass)) {
                return;
            }

            // find parent list of item under cursor
            var pointElRoot = this.pointEl.closest('.' + opt.rootClass),
                isNewRoot = this.dragRootEl.data('nestable-id') !== pointElRoot.data('nestable-id');

            /**
             * move vertical
             */
            if (!mouse.dirAx || isNewRoot || isEmpty) {
                // check if groups match if dragging over new root
                if (isNewRoot && opt.group !== pointElRoot.data('nestable-group')) {
                    return;
                }
                // check depth limit
                depth = this.dragDepth - 1 + this.pointEl.parents(opt.listNodeName).length;
                if (depth > opt.maxDepth) {
                    return;
                }
                var before = e.pageY < (this.pointEl.offset().top + this.pointEl.height() / 2);
                parent = this.placeEl.parent();
                // if empty create new list to replace empty placeholder
                if (isEmpty) {
                    tree = $(document.createElement(opt.listNodeName)).addClass(opt.listClass);
                    tree.append(this.placeEl);
                    this.pointEl.replaceWith(tree);
                }
                else if (before) {
                    this.pointEl.before(this.placeEl);
                }
                else {
                    this.pointEl.after(this.placeEl);
                }
                if (!parent.children().length) {
                    this.unsetParent(parent.parent());
                }
                if (!this.dragRootEl.find(opt.itemNodeName).length) {
                    this.dragRootEl.append('<div class="' + opt.emptyClass + '"/>');
                }
                // parent root list has changed
                if (isNewRoot) {
                    this.dragRootEl = pointElRoot;
                    this.hasNewRoot = this.el[0] !== this.dragRootEl[0];
                }
            }
        }

    };

    $.fn.nestable = function (params) {
        var lists = this,
            retval = this;

        lists.each(function () {
            var plugin = $(this).data("nestable");

            if (!plugin) {
                $(this).data("nestable", new Plugin(this, params));
                $(this).data("nestable-id", new Date().getTime());
            } else {
                if (typeof params === 'string' && typeof plugin[params] === 'function') {
                    retval = plugin[params]();
                }
            }
        });

        return retval || lists;
    };

})(window.jQuery || window.Zepto, window, document);
