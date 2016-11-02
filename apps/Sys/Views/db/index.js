var ThisPage = (function(){
    var to = false;
    var vm_db = avalon.define({
        $id: 'dbController',
        items: [],
        obj: {}, 
        page: 1,
        keyword: '',
        loadData: function() {
            wedo.req('search', {page: vm_db.page, keyword: vm_db.keyword}, function(cmd, resp, sent) {
                vm_db.items = resp.data;
                var pg = $('.wd-pagination', '#db_table');
                Pagination.simple(pg, resp.total, resp.page, 20, function(page) {
                    vm_db.page = page;
                    vm_db.loadData();
                }); 
            });
        },
        search: function(e) {
            e.preventDefault();            
            if(to) { clearTimeout(to); }
            to = setTimeout(function () {
                vm_db.loadData();
            }, 250);
        },
        // 保存信息
        save: function(e) {
            e.preventDefault(); 
            if (! Validate.check('#form_edit')) {
                wedo.alert('存在不符合验证规则的输入项！', 'error');
                return;
            }

            wedo.req('save', vm_db.dict, function(cmd, respdata, sentdata) {
                vm_db.loadData();
            });
        },
        // 新增数据库配置
        add: function(e) {
            e.preventDefault(); 
            vm_db.obj = {};
            $('#name').focus();
        },
        
        delete: function(e) {
            e.preventDefault();
            wedo.req('delete', {id:vm_db_dict.dict.id}, function(cmd, respdata, sentdata) {
                vm_db_dict.dicts.removeAt(vm_db_dict.curr_index);
                vm_db_dict.newDict(e);
            });
        },
        
        // save: function(e, index) {
        //     e.preventDefault();
        //     var $tr = $(e.target).closest('tr');
        //     if (! Validate.check($tr)) {
        //         wedo.alert('存在不符合验证规则的输入项！', 'error');
        //         return;
        //     }
            
        //     var old_item = vm_db_dict.items[index];
        //     var item = vm_db_dict.getDictItemFromTr($tr);
        //     if (item.value == old_item.value && item.title == old_item.title && item.display_order == old_item.display_order) {
        //         console.log('没有修改');
        //         return;
        //     }

        //     wedo.req('save-item', item, function(cmd, respdata, sentdata) {
        //         $('[name="id"]', $tr).val(respdata.id);
        //         item = $.extend({}, item, {id: respdata.id, writable: false});                
        //         vm_db_dict.items.set(index, item);
        //         vm_db_dict.sortItems();
        //     });
        // },

        getDictItemFromTr: function($tr) {
            var id = $('[name="id"]', $tr).val();
            var value = $('[name="value"]', $tr).val();
            var title = $('[name="title"]', $tr).val();
            var display_order = $('[name="display_order"]', $tr).val();
            return {id: id, dict_id: vm_db_dict.dict.id, value: value, title: title, display_order: display_order};
        },
    });

    return {
        init: function() {
            vm_db.loadData();
            // $('#btn_new_dict').on('click', function(e){
            //     vm_dict.newDict(e);
            // });
        },   
    };
})();

$(function(){
    ThisPage.init();
    avalon.scan(document.getElementById('#db_page'));
});