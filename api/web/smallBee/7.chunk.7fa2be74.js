webpackJsonp([7],{169:function(t,s,i){"use strict";function a(t){l||i(222)}Object.defineProperty(s,"__esModule",{value:!0});var e=i(191),n=i(223),l=!1,o=i(23),c=a,r=o(e.a,n.a,!1,c,"data-v-f6e2b68c",null);r.options.__file="src/module/smallBee/pages/share.vue",s.default=r.exports},183:function(t,s,i){t.exports=i.p+"img/df81ee1f.telephone.png"},191:function(t,s,i){"use strict";(function(t){var a=i(64),e=i.n(a),n=i(145);s.a={name:"share",data:function(){return{loading:0,apiToken:"",name:"",lable:[],certfifcates:"",dked:"",img:"",liucheng:"",process:"",loanTime:"",maxfybl:"",minfybl:"",perMil:"",question:"",teamID:""}},components:{loading:n.a},methods:{initia:function(){var s=this;console.log(222),t.ajax({url:"https://zxgybj.com/api/from-share?shareID=27",type:"GET",success:function(t){var i=t.data;console.log("分享页："+e()(t)),200==i.code?(s.name=i.proDetail.name,s.img=i.proDetail.img,s.lable=i.proDetail.biaoq,s.dked=i.proDetail.dked,s.loanTime=i.proDetail.loanTime,s.perMil=i.proDetail.perMil,s.process=i.proDetail.liucheng,s.certfifcates=i.proDetail.certfifcate,s.question=i.proDetail.question,s.loading=1):s.$store.dispatch("changeTipHint",t.message)}})}},created:function(){this.initia()}}}).call(s,i(38))},222:function(t,s){},223:function(t,s,i){"use strict";var a=function(){var t=this,s=t.$createElement,i=t._self._c||s;return i("div",{staticClass:"mine"},[i("div",{staticClass:"title"},[t._v("产品详情")]),t._v(" "),i("div",{staticClass:"headBox"},[t._m(0),t._v(" "),i("div",{staticClass:"info"},[i("div",{staticClass:"titleBox"},[t.img?i("img",{attrs:{src:t.img}}):t._e()]),t._v(" "),i("div",{staticClass:"infoBox"},[i("div",{staticClass:"infoUp"},[i("div",{staticClass:"text1"},[t._v(t._s(t.name))]),t._v(" "),t.maxfybl?i("div",{staticClass:"cut"},[i("div",{staticClass:"text2"},[t._v(t._s("成功放贷返"+t.minfybl+"% -"+t.maxfybl+"%"))])]):t._e()]),t._v(" "),i("div",{staticClass:"lableBox"},t._l(t.lable,function(s){return i("span",{staticClass:"lable"},[t._v(t._s(s))])}))])])]),t._v(" "),i("div",{staticClass:"neckBox"},[i("div",{staticClass:"neckInfoBox right"},[i("div",{staticClass:"neckTextUp"},[t._v(t._s(t.dked))]),t._v(" "),i("div",{staticClass:"neckTextDown"},[t._v("最高额度")])]),t._v(" "),i("div",{staticClass:"neckInfoBox right"},[i("div",{staticClass:"neckTextUp"},[t._v(t._s(t.loanTime))]),t._v(" "),i("div",{staticClass:"neckTextDown"},[t._v("最快放贷时间")])]),t._v(" "),i("div",{staticClass:"neckInfoBox"},[i("div",{staticClass:"neckTextUp"},[t._v(t._s(t.perMil+"%"))]),t._v(" "),i("div",{staticClass:"neckTextDown"},[t._v("月利率低至")])])]),t._v(" "),i("div",{staticClass:"bodyBox"},[i("div",{staticClass:"payrollBox",domProps:{innerHTML:t._s(t.process)}})]),t._v(" "),t._m(1),t._v(" "),i("div",{staticClass:"bodyBox marginCut"},[i("div",{staticClass:"payrollBox",domProps:{innerHTML:t._s(t.certfifcates)}})]),t._v(" "),i("div",{staticClass:"bodyBox marginCut"},[i("div",{staticClass:"payrollBox",domProps:{innerHTML:t._s(t.question)}}),t._v(" "),t._m(2)]),t._v(" "),i("loading",{attrs:{loading:t.loading}})],1)},e=[function(){var t=this,s=t.$createElement,a=t._self._c||s;return a("div",{staticClass:"member"},[a("div",[t._v("客户经理：张三 13391654132")]),t._v(" "),a("div",{staticClass:"toPhone"},[a("img",{attrs:{src:i(183),alt:""}}),t._v(" "),a("a",{attrs:{href:"tel:13391654132"}})])])},function(){var t=this,s=t.$createElement,i=t._self._c||s;return i("div",{staticClass:"needBox"},[i("div",{staticClass:"need"},[t._v("所需证件：")])])},function(){var t=this,s=t.$createElement,i=t._self._c||s;return i("div",{staticClass:"buttonBox"},[i("div",{staticClass:"button"},[t._v("立即申请")])])}];a._withStripped=!0;var n={render:a,staticRenderFns:e};s.a=n}});