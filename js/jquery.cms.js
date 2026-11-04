jQuery.fn.showFeed = function(options) {

    options = jQuery.extend({
        url: 'rss.php',
        gid: 1,
        pid: false,
        cnt: 20,
        dateFormat: 'yyyy/MM/dd',
        headerTemplate: '<ul>',
        itemTemplate: '<li>{updated}&nbsp;<a href="{link}">{title}</a><br />{description}</li>',
        footerTemplate: '</ul>'
    }, options);

    var target = this;

    if (options.url) {
        jQuery.getFeed({
          url: options.url + '?gid=' + options.gid + '&cnt=' + options.cnt + (options.pid ? '&pid' : ''),
          success: function(feed) {
              var buf = options.headerTemplate;
              for (var i = 0; i < feed.items.length; i++) {
                  var item = feed.items[i];
                  row_t = options.itemTemplate;
                  row_t = row_t.replace("{link}", item.link);
                  row_t = row_t.replace("{title}", item.title);
                  row_t = row_t.replace("{description}", item.description.replace(/<[^>]*>/ig, ""));

                  var dateFormat = new DateFormat(options.dateFormat);
                  var dd = new Date.W3CDTF();
                  dd.setW3CDTF(item.updated);
                  var str = dateFormat.format(dd);
                  row_t = row_t.replace("{updated}", str);

                  buf += row_t;
              }
              buf += options.footerTemplate;
              target.append(buf);
          }
       });
    }
};

jQuery.fn.showCategory = function(options) {

    options = jQuery.extend({
        url: 'category.php',
        gid: 1,
        pid: false,
        headerTemplate: '<ul>',
        itemTemplate: '<li><a href="{link}">{webtitle}</a>&nbsp;({countCategory})</li>',
        emptyItemTemplate: '<li>{webtitle}&nbsp;({countCategory})</li>',
        footerTemplate: '</ul>',
        showEmptyItem: false,
		ajax: false,
    }, options);

    var target = this;

    if (options.url) {
        $.getJSON(options.url + '?gid=' + options.gid + (options.pid ? '&pid' : '') + (options.ajax ? '&ajax=1' : ''), function(json) {
            var buf = options.headerTemplate;
            for (var i = 0; i < json.length; i++) {
                if (json[i].count == '0' && !options.showEmptyItem) {
                    continue;
                }
                row_t = (json[i].count == '0') ? options.emptyItemTemplate : options.itemTemplate;
                row_t = row_t.replace("{link}", json[i].link);
                row_t = row_t.replace("{title}", json[i].title);
                row_t = row_t.replace("{webtitle}", json[i].webtitle);
                row_t = row_t.replace("{countCategory}", json[i].count);
                row_t = row_t.replace("{id}", json[i].id);
                buf += row_t;
            }
            buf += options.footerTemplate;
            target.append(buf);
        });
    }
};
