webpackJsonp([17],{179:function(t,i,a){"use strict";function n(t){o||a(247)}Object.defineProperty(i,"__esModule",{value:!0});var e=a(201),s=a(248),o=!1,r=a(23),c=n,d=r(e.a,s.a,!1,c,"data-v-075c5991",null);d.options.__file="src/module/smallBee/pages/article.vue",i.default=d.exports},201:function(t,i,a){"use strict";(function(t){var n=a(67),e=a(145);i.a={name:"article",data:function(){return{info:"",loading:1}},components:{loading:e.a},methods:{initia:function(){var i=this;t.ajax({url:n.a.ARTICLE_INFO,headers:{token:this.$store.state.userInfo.apiToken},data:{promotionID:this.$route.params.id},type:"GET",success:function(t){var a=t.data;200==a.code?(i.info=a.promotion.content,i.loading=0):i.$store.dispatch("changeTipHint",a.message)}})}},created:function(){this.initia()}}}).call(i,a(38))},247:function(t,i){},248:function(t,i,a){"use strict";var n=function(){var t=this,i=t.$createElement,a=t._self._c||i;return a("div",{staticClass:"mine"},[a("div",{staticClass:"title"},[t._v("图文推广")]),t._v(" "),a("div",{staticClass:"mainInfo"},[a("div",{staticClass:"info",domProps:{innerHTML:t._s(t.info)}})]),t._v(" "),a("loading",{attrs:{loading:t.loading}})],1)},e=[];n._withStripped=!0;var s={render:n,staticRenderFns:e};i.a=s}});