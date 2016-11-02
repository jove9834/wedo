var ThisPage = (function(){
    var check_update_db = false;
    var model = avalon.define({
        $id: 'menu',
        category: {
            id: 0,
            name: ''
        },
        menuitems: [],
        fun: {
            id: 0,
            fun_name: '',
        },
        sub: {
            name: ''
        },
        // 添加分类
        addCategory: function(e) {
            e.preventDefault();
            if (! Validate.check('#category_sub_form')) {
                wedo.alert('存在不符合验证规则的输入项！', 'error');
                return;
            }

            wedo.req('add-category', {pid: model.category.id, name: model.sub.name}, function(cmd, respdata, sentdata) {
                var instance = $('#menutree').jstree(true);
                var selected = instance.get_selected(true)[0];
                console.log(selected);
                if (selected) {
                    if (instance.is_open(selected)) {
                        instance.create_node(selected, {id: respdata.id, text: respdata.name, li_attr: {isFunction: false}, children: true});
                    }
                    else {
                        instance.open_node(selected);    
                    }
                }
                else {
                    instance.create_node('#', {id: respdata.id, text: respdata.name, li_attr: {isFunction: false}, children: true});
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
                var instance = $('#menutree').jstree(true);
                instance.rename_node(instance.get_selected(true), model.category.name);
            });
        },
                
        // 删除分类
        deleteCategory: function(e) {
            e.preventDefault();
            ThisPage.deleteCategory(model.category.id);
            model.category = {id:'', name:'', menus:[]};
        },
    });

    return {
        init: function() {
            // 初始化树
            $('#menutree').jstree({
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
                'types' : {
                    'default' : { 'icon' : 'fa fa-folder' },
                    'item' : { 'valid_children' : [], 'icon' : 'fa fa-file-o' }
                },
                'plugins' : ['state', 'dnd', 'types']
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
                    wedo.req('get-function', {fun_name: id}, function(cmd, respdata, sentdata) {
                        model.fun = {id: respdata.id, name: respdata.fun_name, title: respdata.title, url: respdata.url};
                    });

                    ThisPage.showPanel('fun');
                }
                else {
                    model.category = {id:id, name: node.text};
                    // 取分类下的所有功能
                    wedo.req('menu-function', {id: id}, function(cmd, respdata, sentdata) {
                        model.menuitems = respdata;
                        // 不更新数据库
                        ThisPage.check_node();
                    });

                    ThisPage.showPanel('menu');
                }
            });
            
            // 功能树
            $('#functiontree').jstree({
                'core' : {                
                    'data': {
                        'url' : wedo.setting.base_url + '/sys/function/ajax?cmd=load-tree',
                        "dataType" : "json",
                        'data' : function (node) {
                            return { 'id' : node.id};
                        },
                    },
                },
                'checkbox' : {
                    'keep_selected_style' : true,
                    'tie_selection' : false
                },
                'types' : {
                    'default' : { 'icon' : 'fa fa-folder' },
                    'item' : { 'valid_children' : [], 'icon' : 'fa fa-file-o' }
                },
                'plugins' : ['types', 'checkbox']
            }).on('check_node.jstree', function(e, data){
                if (! check_update_db || ! data.node) {
                    return;
                }

                var ids = [];
                var node = data.node;
                if (node.type == 'item') {
                    if ($.inArray(node.id, model.menuitems) == -1) {
                        var fun_id = node.id.substr(1);
                        ids.push(fun_id);
                    }
                }
                else {
                    // 选中的是分类
                    $.each(node.children, function(index, val) {
                        if ($.inArray(val, model.menuitems) == -1) {
                            var fun_id = val.substr(1);
                            ids.push(val);
                        }
                    });
                }

                if ( ids.length < 1) {
                    return;
                }

                ThisPage.addMenu(ids);
            }).on('uncheck_node.jstree', function(e, data){
                if (! check_update_db || ! data.node) {
                    return;
                }

                var ids = [];
                var node = data.node;
                if (node.type == 'item') {
                    if ($.inArray(node.id, model.menuitems) != -1) {
                        var fun_id = node.id.substr(1);
                        ids.push(fun_id);
                    }
                }
                else {
                    // 选中的是分类
                    $.each(node.children, function(index, val) {
                        if ($.inArray(val, model.menuitems) != -1) {
                            var fun_id = val.substr(1);
                            ids.push(val);
                        }
                    });
                }

                if ( ids.length < 1) {
                    return;
                }

                ThisPage.removeMenu(ids);
            }).on('after_open.jstree', function(e, data){
                ThisPage.check_node();
            });

            // 新增一级分类按钮
            $('#btn_add_top').on('click', function() {
                model.category.id = 0;
                ThisPage.showPanel('top');
            });
        },
                
        deleteCategory: function (id) {
            wedo.confirm('确认要删除吗？', function(){
                wedo.req('delete', {id: id}, function(cmd, respdata, sentdata) {
                    var instance = $('#menutree').jstree(true);
                    instance.delete_node(instance.get_selected(true));
                });
            });
        },

        check_node: function() {
            var instance = $('#functiontree').jstree(true);
            check_update_db = false;
            instance.uncheck_all();
            $.each(model.menuitems, function(index, val) {                
                var node = instance.get_node(val);
                instance.check_node(node);
            });
            check_update_db = true;
        }, 

        addMenu: function(fun_ids) {
            wedo.req('add-menu', {category_id: model.category.id, fun_ids: fun_ids}, function(cmd, respdata, sentdata) {
                var instance = $('#menutree').jstree(true);
                var selected = instance.get_selected(true);
                var node = selected[0];
                instance.load_node(node);
                $.each(fun_ids, function(index, val) {
                    model.menuitems.push(val);  
                });
            });
        },

        removeMenu: function(fun_ids) {
            wedo.req('remove-menu', {category_id: model.category.id, fun_ids: fun_ids}, function(cmd, respdata, sentdata) {
                var instance = $('#menutree').jstree(true);
                var selected = instance.get_selected(true);
                var node = selected[0];
                instance.load_node(node); 
                $.each(fun_ids, function(index, val) {
                    model.menuitems.splice($.inArray(val, model.menuitems), 1);
                });
            });  
        }, 

        showPanel: function(tag) {
            $('#menu_panel').removeClass('hide').addClass('hide');
            $('#function_panel').removeClass('hide').addClass('hide');
            $('#top_category_panel').removeClass('hide').addClass('hide');

            if (tag == 'top') {
                $('#menutree').jstree(true).deselect_all();
                $('#top_category_panel').removeClass('hide');
            }
            else if(tag == 'menu') {
                $('#menu_panel').removeClass('hide');
            }
            else {
                $('#function_panel').removeClass('hide');    
            }
        },
    };
})();

$(function(){
    ThisPage.init();
    
    Validate.scan(document.getElementById('#panel_right'));
    avalon.scan(document.getElementById('#panel_right'));
});
