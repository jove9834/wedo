var ThisPage = (function(){
    var to = false;
    var vm_dict = avalon.define({
        $id: 'dictController',
        dicts: [],
        dict: {},
        items: [],
        page: 1,
        keyword: '',
        curr_index: -1,
        loadDicts: function() {
            wedo.req('search', {page: vm_dict.page, keyword: vm_dict.keyword}, function(cmd, resp, sent) {
                vm_dict.dicts = resp.data;
                vm_dict.curr_index = -1;
                Pagination.simple('#dict_pagination', resp.total, resp.page, 20, function(page) {
                    vm_dict.page = page;
                    vm_dict.loadDicts();
                }); 
            });
        },
        search: function(e) {
            e.preventDefault();            
            if(to) { clearTimeout(to); }
            to = setTimeout(function () {
                vm_dict.loadDicts();
            }, 250);
        },
        // 保存字典信息
        saveDict: function(e) {
            e.preventDefault(); 
            if (! Validate.check('#form_edit')) {
                wedo.alert('存在不符合验证规则的输入项！', 'error');
                return;
            }

            wedo.req('save-dict', vm_dict.dict, function(cmd, respdata, sentdata) {
                if (vm_dict.dict.id > 0) {
                    // 修改
                    vm_dict.dicts.set(vm_dict.curr_index, respdata);
                }
                else {
                    // 添加
                    vm_dict.dicts.push(respdata);
                }
            });
        },
        // 新增字典页面
        newDict: function(e) {
            e.preventDefault(); 
            vm_dict.dict = {};
            vm_dict.items = [];
            vm_dict.curr_index = -1;
            $('#name').focus();
            $('#box_dict > a').tab('show');
            $('#box_items').addClass('hide');
            $('#box_items_c').addClass('hide');
            $('#btn_delete_dict').addClass('hide');
        },
        getDict: function(e, index) {
            var id = $(e.target).data('id');
            vm_dict.curr_index = index;
            wedo.req('get-dict', {id: id}, function(cmd, resp, sent) {
                vm_dict.dict = $.extend({}, resp.dict);
                vm_dict.items = resp.items;
                $('#box_items').removeClass('hide');
                $('#box_items_c').removeClass('hide');
                $('#btn_delete_dict').removeClass('hide');
            });
        },
        deleteDict: function(e) {
            e.preventDefault();
            wedo.req('delete', {id:vm_dict.dict.id}, function(cmd, respdata, sentdata) {
                vm_dict.dicts.removeAt(vm_dict.curr_index);
                vm_dict.newDict(e);
            });
        },
        addItem: function(e) {
            e.preventDefault(); 
            vm_dict.items.push({id:'', value: '', title: '', display_order: 0, writable: true});
        },

        editItem: function(e, index) {
            e.preventDefault(); 
            var item = vm_dict.items[index];
            if (item.writable == true) {
                // 已经是编辑状态
                return;
            }

            item = $.extend({}, item, {writable: true});
            vm_dict.items.set(index, item);
        },

        deleteItem: function(e, index) {
            e.preventDefault();
            var item = vm_dict.items[index];
            if (item.writable && !item.id) {
                vm_dict.items.removeAt(index); 
                return;
            }

            wedo.req('delete-item', {id:item.id}, function(cmd, respdata, sentdata) {
                vm_dict.items.removeAt(index); 
                vm_dict.sortItems();
            });
        },

        saveItem: function(e, index) {
            e.preventDefault();
            var $tr = $(e.target).closest('tr');
            if (! Validate.check($tr)) {
                wedo.alert('存在不符合验证规则的输入项！', 'error');
                return;
            }
            
            var old_item = vm_dict.items[index];
            var item = vm_dict.getDictItemFromTr($tr);
            if (item.value == old_item.value && item.title == old_item.title && item.display_order == old_item.display_order) {
                console.log('没有修改');
                return;
            }

            wedo.req('save-item', item, function(cmd, respdata, sentdata) {
                $('[name="id"]', $tr).val(respdata.id);
                item = $.extend({}, item, {id: respdata.id, writable: false});                
                vm_dict.items.set(index, item);
                vm_dict.sortItems();
            });
        },

        getDictItemFromTr: function($tr) {
            var id = $('[name="id"]', $tr).val();
            var value = $('[name="value"]', $tr).val();
            var title = $('[name="title"]', $tr).val();
            var display_order = $('[name="display_order"]', $tr).val();
            return {id: id, dict_id: vm_dict.dict.id, value: value, title: title, display_order: display_order};
        },

        sortItems: function() {
            vm_dict.items.sort(function(a, b) {
                return b.display_order - a.display_order;
            });
        }
    });

    return {
        init: function() {
            vm_dict.loadDicts();
            $('#btn_new_dict').on('click', function(e){
                vm_dict.newDict(e);
            });
        },   
    };
})();

$(function(){
    ThisPage.init();
    avalon.scan(document.getElementById('#dict_page'));
});