
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
lessTags[t].remove();});}},init=function(){$(function(){saveLess();request();monitor();});return core;};core.locChange=function(func){func();$(window).trigger('locchange');};return init();}(jQuery));﻿
{var settings={saveless:{path:'/assets/css/'}};var routes=[];};window.DEV=true;