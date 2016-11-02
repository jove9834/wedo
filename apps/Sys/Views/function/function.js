var ThisPage = (function(){
    var to = false;
    var model = avalon.define({
        $id: 'category',
        category: {
            id: 0,
            name: ''
        },
        fun: {},
        sub: {
            name: ''
        },
        rightitems: [],
        rightitem: {},
        keyword: '',
        // 添加分类
        addCategory: function(e) {
            e.preventDefault();
            if (! Validate.check('#category_sub_form')) {
                wedo.alert('存在不符合验证规则的输入项！', 'error');
                return;
            }

            wedo.req('add-category', {pid: model.category.id, name: model.sub.name}, function(cmd, respdata, sentdata) {
                var instance = $('#function_category').jstree(true);
                var selected = instance.get_selected(true)[0];
                if (selected) {
                    if (instance.is_open(selected)) {
                        instance.create_node(selected, {id: respdata.id, text: respdata.name, li_attr: {isFunction: false}, children: true});
                    }
                    else {
                        instance.open_node(selected);    
                    }
                }
                else {
                    instance.create_node(null, {id: respdata.id, text: respdata.name, li_attr: {isFunction: false}, children: true});
                }
                
                model.sub.name = '';
            });
        },

        // 分类更名
        rename: function(e) {
            e.preventDefault();
            if (! Validate.check('#category_edit_form')) {
                wedo.alert('存在不符合验证规则的输入项！', 'error');
                return;
            }

            wedo.req('category-rename', {id: model.category.id, name: model.category.name}, function(cmd, respdata, sentdata) {
                var instance = $('#function_category').jstree(true);
                instance.rename_node(instance.get_selected(true), model.category.name);
            });
        },
        // 添加功能
        addFunction: function(e) {
            e.preventDefault();
            if (! Validate.check('#category_fun_form')) {
                wedo.alert('存在不符合验证规则的输入项！', 'error');
                return;
            }

            model.fun.category_id = model.category.id;
            wedo.req('add-function', wedo.getJsonData(model.fun), function(cmd, respdata, sentdata) {
                var instance = $('#function_category').jstree(true);
                var selected = instance.get_selected(true)[0];
                if (instance.is_open(selected)) {
                    instance.create_node(instance.get_selected(true)[0], {id: 'F' + respdata.id, text: respdata.title, type: 'item', li_attr: {isFunction: true}});
                }
                else {
                    instance.open_node(selected);    
                }

                model.fun = {id: '', name:'', title:'', url: ''};
            });
        },
        // 更新功能信息
        updateFunction: function(e) {
            e.preventDefault();
            if (! Validate.check('#edit_fun_form')) {
                wedo.alert('存在不符合验证规则的输入项！', 'error');
                return;
            }

            wedo.req('update-function', wedo.getJsonData(model.fun), function(cmd, respdata, sentdata) {
                var instance = $('#function_category').jstree(true);
                instance.rename_node(instance.get_selected(true), model.fun.title);
            });
        },
        // 删除分类
        deleteCategory: function(e) {
            e.preventDefault();
            ThisPage.delete(model.category.id, false);
        },

        deleteFunction: function(e) {
            e.preventDefault();
            ThisPage.delete(model.fun.id, true);
        },

        search: function(e) {
            e.preventDefault();            
            if(to) { clearTimeout(to); }
            to = setTimeout(function () {
                $('#function_category').jstree(true).search(model.keyword, false);
            }, 250);
        },

        newRightItem: function(e) {
            model.rightitem = $.extend({}, {id: 0, fun_id: model.fun.id});
            
            if (e) {
                e.preventDefault(); 
                $(e.target).parent().find('.btn-primary').removeClass('btn-primary').addClass('btn-white');
                $(e.target).removeClass('btn-white').addClass('btn-primary');
            };
        }, 

        showRightItem: function(id, e) {
            e.preventDefault(); 
            $.each(model.rightitems, function(index, val) {
                if (val.id == id) {
                    model.rightitem = $.extend({}, val);
                    return true;
                }
            });

            $(e.target).parent().find('.btn-primary').removeClass('btn-primary').addClass('btn-white');
            $(e.target).removeClass('btn-white').addClass('btn-primary');
        },
        // 保存权限项
        saveRightItem: function(e) {
            e.preventDefault(); 
            if (! Validate.check('#rightitem_form')) {
                wedo.alert('存在不符合验证规则的输入项！', 'error');
                return;
            }

            wedo.req('save-right-item', wedo.getJsonData(model.rightitem), function(cmd, respdata, sentdata) {
                if (model.rightitem.id > 0) {
                    // 修改
                    $.each(model.rightitems, function(index, val) {
                        if (val.id == model.rightitem.id) {
                            model.rightitems[index] = model.rightitem;
                            return true;
                        }
                    });
                }
                else {
                    // 添加
                    model.rightitems.push(respdata);
                }
            });
        },

        deleteRightItem: function(e) {
            // 删除权限项
            e.preventDefault(); 
            if (model.rightitem.id <= 0) {
                wedo.alert('权限项不存在!', 'error');
                return;
            }

            wedo.req('delete-right-item', {id:model.rightitem.id}, function(cmd, respdata, sentdata) {
                // 修改
                $.each(model.rightitems, function(index, val) {
                    if (val.id == model.rightitem.id) {
                        model.rightitems.removeAt(index);
                        model.newRightItem();
                        return true;
                    }
                });
            });
        }

    });

    return {
        init: function() {
            // 初始化树
            $('#function_category').jstree({
                'core' : {                
                    'data': {
                        'url' : wedo.setting.ajax_url + '?cmd=load-tree',
                        "dataType" : "json",
                        'data' : function (node) {
                            return { 'id' : node.id};
                        },
                    },
                    'check_callback' : function(o, n, p, i, m) {
                        // if(m && m.dnd && m.pos !== 'i') { return false; }
                        // console.log(p);
                        if(o === "move_node") {
                            if(this.get_node(p).type == 'item') { return false; }
                        }

                        return true;
                    },                    
                },
                search: {
                    case_insensitive: true,
                    ajax: function(str, callback) {
                        wedo.req('search', {keyword: str}, function(cmd, respdata, sentdata) {
                            for (var i = 0; i < respdata.length; i++) {
                                callback(respdata[i]);
                            }
                        });
                    }
                },
                'types' : {
                    'default' : { 'icon' : 'fa fa-folder' },
                    'item' : { 'valid_children' : [], 'icon' : 'fa fa-file-o' }
                },
                'plugins' : ['state','dnd', 'types', 'search']
            })            
            .on('move_node.jstree', function (e, data) {
                // wedo.confirm('确认移动？', function() {
                    // OK
                    var pid = data.node.parent;
                    var p = data.instance.get_node(pid);
                    if (pid == '#') {
                        pid = 0;
                    }

                    var children = p.children;                                    
                    wedo.req('save-drag', {'pid': pid, 'children': children});
                // }, function() {
                //     // cancel
                //     // data.instance.refresh();
                //     data.instance.refresh_node(data.parent);
                // });
                
            })
            .on('changed.jstree', function (e, data) {
                if(!data || ! (data.selected && data.selected.length) || data.action != 'select_node') {
                    return;
                }

                var node = data.node;
                var id = node.id;
                if (node.li_attr.isFunction) {
                    id = id.substr(1);
                    wedo.req('get-function', {id: id}, function(cmd, respdata, sentdata) {
                        model.fun = respdata.fun;
                        model.rightitems = respdata.items;
                        model.newRightItem();
                        $('button:first', '#ri_btn_group').addClass('btn-primary');
                    });

                    $('#top_category_panel').removeClass('hide').addClass('hide');
                    $('#category_panel').removeClass('hide').addClass('hide');
                    $('#function_panel').removeClass('hide');
                }
                else {
                    model.category = {id:id, name: node.text};
                    model.fun = {type: 1};
                    $('#top_category_panel').removeClass('hide').addClass('hide');
                    $('#category_panel').removeClass('hide');
                    $('#function_panel').removeClass('hide').addClass('hide');
                }
            });

            $('#btn_add_first').on('click', function() {
                model.category.id = 0;
                ThisPage.showTopCategoryPanel();
            });

            $('#radio1', '#category_fun_form').on('click', function(){
                model.fun.type = 1;
            });

            $('#radio2', '#category_fun_form').on('click', function(){
                model.fun.type = 2;
            });
        },
                
        delete: function (id, isFun) {
            wedo.confirm('确认要删除吗？', function(){
                wedo.req('delete', {id: id, is_fun: isFun}, function(cmd, respdata, sentdata) {
                    var instance = $('#function_category').jstree(true);
                    instance.delete_node(instance.get_selected(true));
                    $('#category_panel').removeClass('hide').addClass('hide');
                    $('#function_panel').removeClass('hide').addClass('hide');
                });
            });
        },

        showTopCategoryPanel: function() {
            $('#function_category').jstree(true).deselect_all();
            $('#top_category_panel').removeClass('hide');
            $('#category_panel').removeClass('hide').addClass('hide');
            $('#function_panel').removeClass('hide').addClass('hide');
        }, 
    };
})();

$(function(){
    ThisPage.init();
    
    Validate.scan(document.getElementById('#panel_right'));
    avalon.scan(document.getElementById('#panel_right'));
});
