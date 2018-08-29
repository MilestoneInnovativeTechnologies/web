// JavaScript Document
	$(function(){
		$('[name="search_partner"]').autocomplete({
			minLength:	1,
			source: "/api/v1/capi/get/000ps",
			select: function( event, ui ) { $('[name="btn_search_partner"]').attr({href:GetNavLink(ui.item)}); $('[name="search_partner"]').val(ui.item.name); return false; }
		}).autocomplete( "instance" )._renderItem = function( ul, item ) {
      return $( "<li>" )
        .append( "<div>" + item.name + "<em class='pull-right'><small>" + item.roles[0].displayname + "</small></em></div>" )
        .appendTo( ul );
    };
		$('[name="search_ticket"]').autocomplete({
			minLength:	1,
			source: "/api/v1/capi/get/0stkt",
			select: function( event, ui ) { $('[name="btn_search_ticket"]').attr({href:GetTktNavLink(ui.item)}); $('[name="search_ticket"]').val(ui.item.title); return false; }
		}).autocomplete( "instance" )._renderItem = function( ul, item ) {
      return $( "<li>" ).css('border-bottom','1px solid #DDD')
        .append( "<div><b>Code</b>: " + item.code + ", <b>Status</b>: " + item.cstatus.status + "<br><b>Title</b>: " + item.title + "<br><b>Customer</b>: " + item.customer.name + ", <b>Product</b>: " + [item.product.name,item.edition.name,"Edition"].join(" ") + "</div>" )
        .appendTo( ul );
    };
	});
	function GetTktNavLink(Obj){
		return _TKTNavLink.replace('--code--',Obj.code);
	}
	function GetNavLink(Obj){
		return _PartnerNavLink[Obj.roles[0].name].replace('--code--',Obj.code);
	}
