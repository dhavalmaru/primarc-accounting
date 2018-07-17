/**
 * An easy tree view plugin for jQuery and Bootstrap
 * @Copyright yuez.me 2014
 * @Author yuez
 * @Version 0.1
 */
(function ($) {
    $.fn.EasyTree = function (options) {
        var defaults = {
            selectable: true,
            deletable: false,
            editable: false,
            addable: false,
            i18n: {
                deleteNull: 'Select a type to delete',
                deleteRoot: 'Cannot delete root parent type',
                deleteParent: 'Cannot delete parent type',
                deleteConfirmation: 'Delete this type?',
                confirmButtonLabel: 'Yes',
                editNull: 'Select a type to edit',
                editMultiple: 'Only one type can be edited at one time',
                addMultiple: 'Select a type to add a new type',
                addDifferent: 'Entered type already exist please add different type',
                collapseTip: 'collapse',
                expandTip: 'expand',
                selectTip: 'select',
                unselectTip: 'unselet',
                editTip: 'edit',
                addTip: 'add',
                deleteTip: 'delete',
                cancelButtonLabel: 'Cancel'
            }
        };

        var warningAlert = $('<div class="alert alert-warning "><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><strong></strong><span class="alert-content"></span> </div> ');
        var dangerAlert = $('<div class="alert alert-danger "><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><strong></strong><span class="alert-content"></span> </div> ');

        var createInput = $('<form><div class="input-group"><input type="text" class="form-control">  </div> <br><div class="input-group"><span class="input-group-btn"><button type="button" class="btn btn-success btn-sm confirm"></button> </span><span class="input-group-btn"><button type="button" class="btn btn-danger btn-sm cancel"></button> </span></div>');

        options = $.extend(defaults, options);

        this.each(function () {
            var easyTree = $(this);
            $.each($(easyTree).find('ul > li'), function() {
                var text;
                if($(this).is('li:has(ul)')) {
                    var children = $(this).find(' > ul');
                    $(children).remove();
                    text = $(this).text();
                    $(this).html('<span><span class="glyphicon"></span><a href="javascript: void(0);"></a> </span>');
                    $(this).find(' > span > span').addClass('glyphicon-folder-open');
                    $(this).find(' > span > a').text(text);
                    $(this).append(children);
                }
                else {
                    text = $(this).text();
                    $(this).html('<span><span class="glyphicon"></span><a href="javascript: void(0);"></a> </span>');
                    $(this).find(' > span > span').addClass('glyphicon-file');
                    $(this).find(' > span > a').text(text);
                }
            });

            $(easyTree).find('li:has(ul)').addClass('parent_li').find(' > span').attr('title', options.i18n.collapseTip);

            // add easy tree toolbar dom
            if (options.deletable || options.editable || options.addable) {
                $(easyTree).prepend('<div class="easy-tree-toolbar"></div> ');
            }

            // addable
            if (options.addable) {
                $(easyTree).find('.easy-tree-toolbar').append('<div class="create"><button class="btn btn-default btn-sm btn-success"><span class="glyphicon glyphicon-plus"></span></button></div> ');
                $(easyTree).find('.easy-tree-toolbar .create > button').attr('title', options.i18n.addTip).click(function () {
                    var selected = getSelectedItems();
                    if (selected.length <= 0) {
                        $(easyTree).prepend(warningAlert);
                        $(easyTree).find('.alert .alert-content').html(options.i18n.addMultiple);
                    } else if (selected.length > 1) {
                        $(easyTree).prepend(warningAlert);
                        $(easyTree).find('.alert .alert-content').html(options.i18n.editMultiple);
                    } else {
                        var createBlock = $(easyTree).find('.easy-tree-toolbar .create');
                        $(createBlock).append(createInput);
                        $(createInput).find('input').focus();
                        $(createInput).find('.confirm').text(options.i18n.confirmButtonLabel);
                        $(createInput).find('.confirm').click(function () {
                            if ($(createInput).find('input').val() === '')
                                return;
                            var selected = getSelectedItems();
                            
                            var parent_id = 0;
                            var item_id = selected.attr('id');
                            if(item_id){
                                if(item_id.indexOf('_') != -1){
                                    parent_id = item_id.substring(item_id.lastIndexOf('_')+1);
                                }
                            }
                            
                            var account_type = $(createInput).find('input').val();
                            var new_type_id = 0;
                            var action = 'insert';

                            var result = get_account_type(action, parent_id, account_type);
                            if(result==false){
                                $(easyTree).prepend(warningAlert);
                                $(easyTree).find('.alert .alert-content').html(options.i18n.addDifferent);
                                return false;
                            } else {
                                $(warningAlert).remove();
                            }

                            result = set_account_type(action, parent_id, account_type);
                            if(result==false){
                                return false;
                            } else {
                                new_type_id = result;
                            }

                            var item = $('<li id="type_' + new_type_id + '"><span><span class="glyphicon glyphicon-file"></span><a href="javascript: void(0);">' + $(createInput).find('input').val() + '</a> </span></li>');
                            $(item).find(' > span > span').attr('title', options.i18n.collapseTip);
                            $(item).find(' > span > a').attr('title', options.i18n.selectTip);
                            if (selected.length <= 0) {
                                $(easyTree).find(' > ul').append($(item));
                            } else if (selected.length > 1) {
                                $(easyTree).prepend(warningAlert);
                                $(easyTree).find('.alert .alert-content').text(options.i18n.addMultiple);
                            } else {
                                if ($(selected).hasClass('parent_li')) {
                                    $(selected).find(' > ul').append(item);
                                } else {
                                    $(selected).addClass('parent_li').find(' > span > span').addClass('glyphicon-folder-open').removeClass('glyphicon-file');
                                    $(selected).append($('<ul></ul>')).find(' > ul').append(item);
                                }
                            }
                            $(createInput).find('input').val('');
                            if (options.selectable) {
                                $(item).find(' > span > a').attr('title', options.i18n.selectTip);
                                $(item).find(' > span > a').click(function (e) {
                                    var li = $(this).parent().parent();
                                    if (li.hasClass('li_selected')) {
                                        $(this).attr('title', options.i18n.selectTip);
                                        $(li).removeClass('li_selected');
                                    }
                                    else {
                                        $(easyTree).find('li.li_selected').removeClass('li_selected');
                                        $(this).attr('title', options.i18n.unselectTip);
                                        $(li).addClass('li_selected');
                                    }

                                    if (options.deletable || options.editable || options.addable) {
                                        var selected = getSelectedItems();
                                        if (options.editable) {
                                            if (selected.length <= 0 || selected.length > 1)
                                                $(easyTree).find('.easy-tree-toolbar .edit > button').addClass('disabled');
                                            else
                                                $(easyTree).find('.easy-tree-toolbar .edit > button').removeClass('disabled');
                                        }

                                        if (options.deletable) {
                                            if (selected.length <= 0 || selected.length > 1)
                                                $(easyTree).find('.easy-tree-toolbar .remove > button').addClass('disabled');
                                            else
                                                $(easyTree).find('.easy-tree-toolbar .remove > button').removeClass('disabled');
                                        }

                                    }

                                    e.stopPropagation();

                                });
                            }
                            $(createInput).remove();
                        });
                        $(createInput).find('.cancel').text(options.i18n.cancelButtonLabel);
                        $(createInput).find('.cancel').click(function () {
                            $(createInput).remove();
                        });
                    }
                });
            }

            // editable
            if (options.editable) {
                $(easyTree).find('.easy-tree-toolbar').append('<div class="edit"><button class="btn btn-default btn-sm btn-primary disabled"><span class="glyphicon glyphicon-edit"></span></button></div> ');
                $(easyTree).find('.easy-tree-toolbar .edit > button').attr('title', options.i18n.editTip).click(function () {
                    $(easyTree).find('input.easy-tree-editor').remove();
                    $(easyTree).find('li > span > a:hidden').show();
                    var selected = getSelectedItems();
                    if (selected.length <= 0) {
                        $(easyTree).prepend(warningAlert);
                        $(easyTree).find('.alert .alert-content').html(options.i18n.editNull);
                    }
                    else if (selected.length > 1) {
                        $(easyTree).prepend(warningAlert);
                        $(easyTree).find('.alert .alert-content').html(options.i18n.editMultiple);
                    }
                    else {
                        var value = $(selected).find(' > span > a').text();
                        $(selected).find(' > span > a').hide();
                        $(selected).find(' > span').append('<input type="text" class="easy-tree-editor">');
                        var editor = $(selected).find(' > span > input.easy-tree-editor');
                        $(editor).val(value);
                        $(editor).focus();
                        $(editor).keydown(function (e) {
                            if (e.which == 13) {
                                if ($(editor).val() !== '') {
                                    var item_id = selected.attr('id');
			                        var parent_id = 0;
			                        if(item_id.indexOf('_') != -1){
			                        	parent_id = item_id.substring(item_id.lastIndexOf('_')+1);
			                        }
			                        var account_type = $(editor).val();
			                        var action = 'update';

                                    var result = get_account_type(action, parent_id, account_type);
                                    if(result==false){
                                        $(easyTree).prepend(warningAlert);
                                        $(easyTree).find('.alert .alert-content').html(options.i18n.addDifferent);
                                        return false;
                                    } else {
                                        $(warningAlert).remove();
                                    }

			                        result = set_account_type(action, parent_id, account_type);
			                        if(result==false){
			                        	return false;
			                        }

                                    $(selected).find(' > span > a').text($(editor).val());
                                    $(editor).remove();
                                    $(selected).find(' > span > a').show();
                                }
                            }
                        });
                    }
                });
            }

            // deletable
            if (options.deletable) {
                $(easyTree).find('.easy-tree-toolbar').append('<div class="remove"><button class="btn btn-default btn-sm btn-danger disabled"><span class="glyphicon glyphicon-remove"></span></button></div> ');
                $(easyTree).find('.easy-tree-toolbar .remove > button').attr('title', options.i18n.deleteTip).click(function () {
                    var selected = getSelectedItems();
                    if (selected.length <= 0) {
                        $(easyTree).prepend(warningAlert);
                        $(easyTree).find('.alert .alert-content').html(options.i18n.deleteNull);
                    } else {
                        var item_id = selected.attr('id');
                        var parent_id = 0;
                        if(item_id.indexOf('_') != -1){
                            parent_id = item_id.substring(item_id.lastIndexOf('_')+1);
                        }
                        if(parent_id=='1' || parent_id=='2' || parent_id=='3' || parent_id=='4'){
                            $(easyTree).prepend(warningAlert);
                            $(easyTree).find('.alert .alert-content').html(options.i18n.deleteRoot);
                        } else {
                            var result = get_child_account_type(parent_id);
                            if(result==1){
                                $(easyTree).prepend(warningAlert);
                                $(easyTree).find('.alert .alert-content').html(options.i18n.deleteParent);
                            } else {
                                $(easyTree).prepend(dangerAlert);
                                $(easyTree).find('.alert .alert-content').html(options.i18n.deleteConfirmation)
                                    .append('<a style="margin-left: 10px;" class="btn btn-default btn-danger confirm"></a>')
                                    .find('.confirm').html(options.i18n.confirmButtonLabel);
                                $(easyTree).find('.alert .alert-content .confirm').on('click', function () {
                                    var account_type = $(selected).find(' > span > a').text();
                                    var action = 'delete';
                                    var result = set_account_type(action, parent_id, account_type);
                                    if(result==false){
                                        return false;
                                    }

                                    $(selected).find(' ul ').remove();
                                    if($(selected).parent('ul').find(' > li').length <= 1) {
                                        $(selected).parents('li').removeClass('parent_li').find(' > span > span').removeClass('glyphicon-folder-open').addClass('glyphicon-file');
                                        $(selected).parent('ul').remove();
                                    }
                                    $(selected).remove();
                                    $(dangerAlert).remove();
                                });
                            }
                        }
                    }
                });
            }

            // collapse or expand
            $(easyTree).delegate('li.parent_li > span', 'click', function (e) {
                var children = $(this).parent('li.parent_li').find(' > ul > li');
                if (children.is(':visible')) {
                    children.hide('fast');
                    $(this).attr('title', options.i18n.expandTip)
                        .find(' > span.glyphicon')
                        .addClass('glyphicon-folder-close')
                        .removeClass('glyphicon-folder-open');
                } else {
                    children.show('fast');
                    $(this).attr('title', options.i18n.collapseTip)
                        .find(' > span.glyphicon')
                        .addClass('glyphicon-folder-open')
                        .removeClass('glyphicon-folder-close');
                }
                e.stopPropagation();
            });

            // selectable, only single select
            if (options.selectable) {
                $(easyTree).find('li > span > a').attr('title', options.i18n.selectTip);
                $(easyTree).find('li > span > a').click(function (e) {
                    var li = $(this).parent().parent();
                    if (li.hasClass('li_selected')) {
                        $(this).attr('title', options.i18n.selectTip);
                        $(li).removeClass('li_selected');
                    }
                    else {
                        $(easyTree).find('li.li_selected').removeClass('li_selected');
                        $(this).attr('title', options.i18n.unselectTip);
                        $(li).addClass('li_selected');
                    }

                    if (options.deletable || options.editable || options.addable) {
                        var selected = getSelectedItems();
                        if (options.editable) {
                            if (selected.length <= 0 || selected.length > 1)
                                $(easyTree).find('.easy-tree-toolbar .edit > button').addClass('disabled');
                            else
                                $(easyTree).find('.easy-tree-toolbar .edit > button').removeClass('disabled');
                        }

                        if (options.deletable) {
                            if (selected.length <= 0 || selected.length > 1)
                                $(easyTree).find('.easy-tree-toolbar .remove > button').addClass('disabled');
                            else
                                $(easyTree).find('.easy-tree-toolbar .remove > button').removeClass('disabled');
                        }

                    }

                    e.stopPropagation();

                });
            }

            // Get selected items
            var getSelectedItems = function () {
                return $(easyTree).find('li.li_selected');
            };
        });
    };
})(jQuery);

(function ($) {
function init() {
    $('.easy-tree').EasyTree({
        addable: true,
        editable: true,
        deletable: true
    });
}

window.onload = init();
})(jQuery)

var get_account_type = function(action, parent_id, account_type){
    var csrfToken = $('meta[name="csrf-token"]').attr("content");
    var result = false;

    if(action=='insert' || action=='update'){
        $.ajax({
            url: BASE_URL+'index.php?r=groupmaster%2Fgetaccounttype',
            type: 'post',
            data: {
                    action : action,
                    parent_id : parent_id,
                    account_type : account_type,
                    _csrf : csrfToken
                },
            dataType: 'json',
            async: false,
            success: function (data) {
                if(data==1){
                    result = false;
                } else {
                    result = true;
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert(xhr.status);
                alert(thrownError);
            }
        });
    } else {
        result = true;
    }

    return result;
}

var set_account_type = function(action, parent_id, account_type){
	var csrfToken = $('meta[name="csrf-token"]').attr("content");
	var result = false;

	$.ajax({
        url: BASE_URL+'index.php?r=groupmaster%2Fsetaccounttype',
        type: 'post',
        data: {
        		action : action,
                parent_id : parent_id,
                account_type : account_type,
                _csrf : csrfToken
            },
        dataType: 'json',
        async: false,
        success: function (data) {
            if(data != null){
                result = data;
            } else {
				result = false;
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
        }
    });

    return result;
}

var get_child_account_type = function(parent_id){
    var csrfToken = $('meta[name="csrf-token"]').attr("content");
    var result = 0;
    $.ajax({
        url: BASE_URL+'index.php?r=groupmaster%2Fgetchildaccounttype',
        type: 'post',
        data: {
                parent_id : parent_id,
                _csrf : csrfToken
            },
        dataType: 'html',
        async: false,
        success: function (data) {
            if(data != null){
                result = data;
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
        }
    });

    return result;
}