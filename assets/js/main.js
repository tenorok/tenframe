var Core=(function($){var core={},parseUrl=function(path){var url=path.split('/');for(var i=0;i<url.length;i++){if(url[i]===''||url[i]==location.protocol||url[i]==location.hostname){url.splice(i,1);i--;}}
return url;},curLocation,request=function(){if(curLocation==location.href)
return;var firstCall=(curLocation===undefined)?true:false;curLocation=location.href;window.ARGS={};var href=parseUrl(curLocation);nextroute:for(var route=0;route<routes.length;route++){var url=routes[route].url,ctrl=routes[route].ctrl,func=routes[route].func,call=routes[route].call||'ever',rules=routes[route].rules||null,pathArr=[];if(typeof(url)=='string')
pathArr[0]=parseUrl(url);else{for(var p=0;p<url.length;p++)
pathArr[p]=parseUrl(url[p]);}
nextpath:for(var p=0;p<pathArr.length;p++){var path=pathArr[p];if(href.length!=path.length||call=='load'&&!firstCall)
continue nextpath;var args=[];for(var part=0;part<path.length;part++){var arg=/^{(.*)}$/.exec(path[part]);if(arg){var rule;if(rules===null||(rule=rules[arg[1]])===undefined||rule.test(href[part])){window.ARGS[arg[1]]=href[part];args.push(href[part]);}
else{window.ARGS={};continue nextpath;}}
else if(href[part]!=path[part]){window.ARGS={};continue nextpath;}}
var method=eval(ctrl)[func];if(method)
method.apply(null,args);else
console.error('Function of Controller is undefined');break nextroute;}}},monitor=function(){if(Modernizr.hashchange)
$(window).on('hashchange popstate locchange',request);else
setInterval(request,500);},saveLess=function(){var set=settings.saveless||null;if(set&&DEV){var links=$('link[rel="stylesheet/less"]');var css={},fileKey,fileHref,lessId,tag,lessTags=[];$.each(links,function(key,value){fileKey=$(value).attr('data-file');fileHref=$(value).attr('href');lessId=fileHref.replace(/\//g,'-').slice(1,-5);css[fileKey]=css[fileKey]||'';tag=$('style#less\\:'+lessId);lessTags.push(tag)
css[fileKey]+=tag.html();});var compress=(set.compress===undefined||set.compress)?true:false;$.post('/sys/ajax/less.php',{event:'save_lesscss',css:JSON.stringify(css),path:set.path,compress:compress},function(){for(var t=0;t<lessTags.length;t++)
lessTags[t].remove();});}},init=function(){$(function(){saveLess();request();monitor();});return core;};core.locChange=function(func){func();$(window).trigger('locchange');};return init();}(jQuery));{var settings={saveless:{path:'/assets/css/'}};var routes=[];};window.DEV=true;$.fn.htmlWithParent=function(){return $("<div/>").append($(this).clone()).html();};function ten_text(text){if(!(this instanceof ten_text))return new ten_text(text);this.text=text;}
ten_text.prototype.translitUri=function(){var exchangeLetters={'А':'A','Б':'B','В':'V','Г':'G','Д':'D','Е':'E','Ё':'E','Ж':'J','З':'Z','И':'I','Й':'Y','К':'K','Л':'L','М':'M','Н':'N','О':'O','П':'P','Р':'R','С':'S','Т':'T','У':'U','Ф':'F','Х':'H','Ц':'TS','Ч':'CH','Ш':'SH','Щ':'SCH','Ъ':'','Ы':'YI','Ь':'','Э':'E','Ю':'YU','Я':'YA','а':'a','б':'b','в':'v','г':'g','д':'d','е':'e','ё':'e','ж':'j','з':'z','и':'i','й':'y','к':'k','л':'l','м':'m','н':'n','о':'o','п':'p','р':'r','с':'s','т':'t','у':'u','ф':'f','х':'h','ц':'ts','ч':'ch','ш':'sh','щ':'sch','ъ':'y','ы':'yi','ь':'','э':'e','ю':'yu','я':'ya',' ':'_'},regexp='';for(key in exchangeLetters)
regexp+=key;return this.text.replace(new RegExp('['+regexp+']','g'),function(str){return str in exchangeLetters?exchangeLetters[str]:'';}).toLowerCase();}﻿
var mod_shop_categories={list:function(){mod_shop_m_categories.init();},add:function(){mod_shop_m_categories_add.init();},edit:function(){mod_shop_m_categories_add.init();mod_shop_m_categories_edit.init();}};routes.push({url:['/admin/','/admin/{page}/','/admin/{page}/{tab}'],ctrl:'mod_shop_categories',func:'list'},{url:['/admin/modshop/categories/add/','/admin/modshop/categories/{parentid}/addcategory/'],ctrl:'mod_shop_categories',func:'add'},{url:'/admin/modshop/categories/{categoryid}/',ctrl:'mod_shop_categories',func:'edit'});var mod_shop_m_categories={init:function(){var obj=mod_shop_m_categories;$('.mod-shop-categories__cat').click(obj.showDropdown);$('body').click(obj.hideDropdown);obj.drawHarr();obj.sortable();},showDropdown:function(){var position=$(this).position(),top=position.top,left=position.left,href=$(this).attr('href'),dropdown='.mod-shop-categories__dropdown__menu_';$('.mod-shop-categories__dropdown').css({'display':'block','top':top+29,'left':left-8});$(dropdown+'add-product').attr({'href':href+'addproduct'});$(dropdown+'add-category').attr({'href':href+'addcategory'});$(dropdown+'edit').attr({'href':href});return false;},hideDropdown:function(){$('.mod-shop-categories__dropdown').css({'display':'none'});},drawHarr:function(){var arrow='<span class="mod-shop-categories__harr">&#9660;</span>';$('.mod-shop-categories__item:has(.mod-shop-categories__item)').children('.mod-shop-categories__name').append(arrow);$('.mod-shop-categories__harr').click(mod_shop_m_categories.slideCategoryList);},slideCategoryList:function(){var arrow=$(this);arrow.parent().next('.mod-shop-categories__list').slideToggle(200,function(){if($(this).is(':hidden'))
arrow.html('&#9650;');else
arrow.html('&#9660;');});},sortable:function(){$('.mod-shop-categories__list').disableSelection().sortable({start:function(e,ui){$(ui.placeholder).hide(300);},change:function(e,ui){$(ui.placeholder).hide().show(300);},tolerance:'pointer',update:mod_shop_m_categories.serializeList,handle:'.mod-shop-categories__draggable',axis:'y',containment:'parent'});},serializeList:function(data){var items=$(data.target).children('.mod-shop-categories__item');var arr=[];$.each(items,function(){var id=$(this).attr('id').split('_')[1];arr.push(id);});$.ajax({type:'GET',url:'/mod/shop/app/ajax/categories.php',data:{event:'sort',categories:arr},dataType:'json'});}};var mod_shop_m_categories_add={fieldHtml:null,init:function(){mod_shop_m_categories_add.fieldHtml=$('.mod-shop-categories__fielditem').htmlWithParent();$('#mod-shop-categories-catname').keyup(mod_shop_m_categories_add.setAlias);$('.mod-shop-categories__fielditem').find('select[name=existfield\\[\\]]').change(mod_shop_m_categories_add.changeExistList);$('.mod-shop-categories__textinput[name=name\\[\\]]').keyup(mod_shop_m_categories_add.addField);$('.mod-shop-categories__checkboxinput').live('change',mod_shop_m_categories_add.changeClassifier);$('.mod-shop-categories__textinput_right-column').live('keyup',mod_shop_m_categories_add.addClassifierValue);},setAlias:function(){$('#mod-shop-categories-catalias').val(ten_text($(this).val()).translitUri());},changeExistList:function(){var labels=$(this).parents('.mod-shop-categories__fielditem').find('.mod-shop-categories__labelitem').not('.mod-shop-categories__existlist');if($(this).val()!='new'){mod_shop_m_categories_add.addField.apply(this);labels.css({'display':'none'});}
else
labels.css({'display':'block'});},addField:function(){if($(this).hasClass('mod-shop-categories__field-added'))
return;var lastField=$('.mod-shop-categories__fielditem').last();lastField.find('select[name=existfield\\[\\]]').addClass('mod-shop-categories__field-added');lastField.find('.mod-shop-categories__textinput[name=name\\[\\]]').addClass('mod-shop-categories__field-added');var fieldsCount=$('.mod-shop-categories__fieldlist').children('.mod-shop-categories__fielditem').length;var newFieldHtml=mod_shop_m_categories_add.fieldHtml.replace(/options_0/g,'options_'+fieldsCount);var newField=$(newFieldHtml).appendTo('.mod-shop-categories__fieldlist');$(newField).find('select[name=existfield\\[\\]]').change(mod_shop_m_categories_add.changeExistList);$(newField).find('.mod-shop-categories__textinput[name=name\\[\\]]').keyup(mod_shop_m_categories_add.addField);},changeClassifier:function(){var classifier=$(this).parents('.mod-shop-categories__labellist').children('.mod-shop-categories__hiddenitem');var hiddenInput=$(this).next();if($(this).is(':checked')){$(classifier).css({'display':'block'});$(classifier).children('.mod-shop-categories__textinput').focus();$(hiddenInput).attr('value','1');}
else{$(classifier).css({'display':'none'});$(hiddenInput).attr('value','0');}},addClassifierValue:function(){if($(this).hasClass('mod-shop-categories__classifier-added'))
return;var parent=$(this).parent('.mod-shop-categories__labelitem');var valCount=parent.children('.mod-shop-categories__textinput').length;var newValueHtml=$($(this).htmlWithParent()).attr('placeholder','Значение '+(++valCount));var newValue=newValueHtml.appendTo(parent);$(this).addClass('mod-shop-categories__classifier-added');$(newValue).keyup(mod_shop_m_categories_add.addClassifierValue);}};var mod_shop_m_categories_edit={init:function(){$('.mod-shop-categories__link_change-parent').click(mod_shop_m_categories_edit.toggleParentForm);$('.mod-shop-categories__cat_edit').click(mod_shop_m_categories_edit.changeParent);},toggleParentForm:function(){var arrow=$(this).children('.mod-shop-categories__harr');$('.mod-shop-categories__list_edit').slideToggle(200,function(){if($(this).is(':hidden'))
arrow.html('&#9650;');else
arrow.html('&#9660;');});return false;},changeParent:function(){if($(this).hasClass('mod-shop-categories__cat_selected')){$(this).removeClass('mod-shop-categories__cat_selected');$('input[name="catparent"]').attr('value','');}
else{$('.mod-shop-categories__cat_edit').removeClass('mod-shop-categories__cat_selected');$(this).addClass('mod-shop-categories__cat_selected');$('input[name="catparent"]').attr('value',$(this).attr('href'));}
return false;}};