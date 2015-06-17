jQuery(document).ready(function() {
	var target = $('#context .actions'),
        li = $('<li class="twitter_post_button" />');
        link = '<url here>';

    if ($('.field-entry_url').length) link = $('.field-entry_url').find('a').attr('href');

    var html = '<a class="twitter-share-button" href="https://twitter.com/intent/tweet?text=Read our latest blog &url=' + link + '" data-count="none" data-dnt="true">Tweet entry</a>';

    li.append(html);
    target.append(li);

    console.log(Symphony.Context.get(0));
});