var Codilar_LayeredNavigation = {
    className: "codilar_layered_navigator_item",
    closeTab: "codilar_selected_filter_close",
    filterLoad: {},
    filters: {},
    init: function(){
        var items = document.getElementsByClassName(Codilar_LayeredNavigation.className),
            i;
        for(i = 0; i < items.length; i++){
            items[i].addEventListener("click", function(){
                Codilar_LayeredNavigation.setFilter(this, this.getAttribute("data-attribute-code"), this.getAttribute("data-attribute-value"));
            });
        }
        Codilar_LayeredNavigation.filterContainer = document.createElement("div");
        document.getElementsByClassName('filter-content')[0].prepend(Codilar_LayeredNavigation.filterContainer);
        Codilar_LayeredNavigation.applyExistingFilters();
    },
    applyExistingFilters: function () {
        Codilar_LayeredNavigation.filterLoad = filtersLoad;
        jQuery('cust-loader').css("display","block");
        var doms = document.getElementsByClassName(Codilar_LayeredNavigation.className),i;
        Codilar_LayeredNavigation.isClearAll = true;
        for (var key in Codilar_LayeredNavigation.filterLoad) {
            if (!Codilar_LayeredNavigation.filterLoad.hasOwnProperty(key)) continue;
            var obj = Codilar_LayeredNavigation.filterLoad[key];
            var filterStore = key !== "price"?obj.split(','):obj.split("-");
            if (key !== "price") {
                for (var start = 0; start < filterStore.length; start++) {
                    for (i = 0; i < doms.length; i++) {
                        var title_current = doms[i].getAttribute('data-attribute-code');
                        var value_current = doms[i].getAttribute('data-attribute-value');
                        if (key == title_current && filterStore[start] == value_current) {
                            doms[i].click();
                        }
                    }
                }
            } else {
                var filters = Codilar_LayeredNavigation.filters;
                var cfil = [];
                var p = filterStore.join('-');
                cfil.push(p);
                if (cfil.length) filters['price'] = cfil;
            }
        }
        Codilar_LayeredNavigation.isClearAll = false;
        Codilar_LayeredNavigation.apply();
    },
    setFilter: function(state,title,value) {
        var filters = Codilar_LayeredNavigation.filters;
        if (title !== 'price') {
            var cfil = typeof filters[title] !== "undefined"?filters[title].slice():[];
            if (state.checked) {
                cfil.push(value);
            }
            else {
                if (cfil.indexOf(value) > -1) {
                    cfil.splice(cfil.indexOf(value), 1);
                }
            }
            if (cfil.length) filters[title] = cfil;
            else delete filters[title];
            Codilar_LayeredNavigation.apply();
        } else {
            var cfil = [];
            cfil.push(value);
            if (cfil.length) filters[title] = cfil;
            else delete filters[title];
            Codilar_LayeredNavigation.apply();
        }
    },
    httpBuildQuery: function(){
        var filters = Object.keys(Codilar_LayeredNavigation.filters);
        var query = [];
        filters.forEach(function(filter){
            query.push(filter+"="+Codilar_LayeredNavigation.filters[filter].join(","));
        });
        query.push('id='+category_id);
        return query.join("&");
    },
    apply: function() {
        if (Codilar_LayeredNavigation.isClearAll) return;
        if(typeof jQuery('body').loader != "undefined") jQuery('body').loader('show');
        var params = Codilar_LayeredNavigation.httpBuildQuery();
        jQuery.get( Codilar_LayeredNavigation.url+"?"+params, function( data, status ) {
            data = JSON.parse(data);
            document.getElementsByClassName('column main')[0].innerHTML = data['products'];
            Codilar_LayeredNavigation.filterContainer.innerHTML = data['filters'];
            // window.history.pushState('','',data['url']);
            if (parseInt(data['config'])) {
                window.history.pushState('','',data['url']);
            }
            else {
                var filters = Object.keys(data['url']);
                var query = [];
                filters.forEach(function(filter){
                    query.push(filter+"="+Codilar_LayeredNavigation.filters[filter].join(","));
                });
                var params =query.join("&");
                var url = data['getUrl'] + '?'+params;
                // window.history.pushState({} , '', '?'+params);
                window.history.pushState('' , '', url);
            }
            if(typeof jQuery('body').loader != "undefined") jQuery('body').loader('hide');
            jQuery('cust-loader').css("display","none");
        });
    },
    disableFilter: function (title,value) {
        var doms = document.getElementsByClassName(Codilar_LayeredNavigation.className);
        var i;
        for(i = 0; i < doms.length; i++){
            var title_current = doms[i].getAttribute('data-attribute-code');
            var value_current = doms[i].getAttribute('data-attribute-value');
            if(title == title_current && value == value_current){
                return doms[i].click();
            }
        }
    },
    disableAllFilters: function () {
        Codilar_LayeredNavigation.isClearAll = true;
        var doms = document.getElementsByClassName(Codilar_LayeredNavigation.className);
        var i;
        for(i = 0; i < doms.length; i++){
                if(doms[i].checked) doms[i].click();
        }
        Codilar_LayeredNavigation.isClearAll = false;
        Codilar_LayeredNavigation.apply();
    },
    priceSlider: function (state,value) {
        Codilar_LayeredNavigation.setFilter(state, "price", value);
    }
}
//if (Codilar_LayeredNavigation.enable) {
    window.addEventListener("load", function () {
        Codilar_LayeredNavigation.init();
    });
//}
