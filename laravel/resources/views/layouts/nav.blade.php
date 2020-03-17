<?php
$_MENU = [
	'company'	=>	[
		'Partner'	=>	[
			['Distributors','distributor'], ['Customers','','customer.index'], ['Support Teams','','tst.index'], ['Partners','','partner.index'], ['Branding','','db.index']//, ['Support Unassigned Customers','','partner.index']
		],
		'Product'	=>	[
			['Products','products'], ['Editions','editions'], ['Packages','packages'], 'divider',
			['Features','features'], ['Price List','pricelist'], 'divider',
            ['FAQ','','faq.index']
        ],
		'Support'	=>	[
			['Departments','','sd.index'], 'divider',
			['Ticket Types','','stt.index'], ['Support Types','','st.index'], 'divider',
			['Ticket Categories','','tcm.index'], ['Category Specification','','tcs.index'], ['Category Report','','category.report'], 'divider',
			['Database Backups','','dbb.index'], ['General Uploads','','gu.index'], ['Public Print Objects','','ppo.index'], 'divider',
			['Service Requests','','sreq.index']
		],
		'Package'	=>	[
			['View Latest Packages','package/latest'], ['Upload Package','package/upload'], ['Verify Package','verify'], ['Delete Package Record','pkg/delete'], ['Approve Package','approve'], ['Revert Package','revert']
		],
		'Mail'	=>	[
			['Updates to Customer/Distributor','','product.update.mailer'], ['Emails','','mail.index'],
		],
		'Registration'	=>	[
			['Registration Requests','regreqs']
		],
        'AMC'	=>	[
            ['Add New','','mc.sc'], ['Upcoming Contracts','','mc.upcoming'], 'divider',
            ['All Active','','mc.index'], ['Expiring Soon','','mc.es'], 'divider',
            ['All Inactive','','mc.inactive'], ['Just Expired','','mc.jexp'],
        ],
		'Misc'	=>	[
			['Log :: Unknown Users','','log.unusr'], ['SMS Gateways','','smsg.index'], 'divider',
			['Public Articles','','pa.index'], ['Private Articles','','pra.index'], 'divider',
			['Notification','','notification.index'], ['Third Party Applications','','tpa.index'], 'divider',
			['Product Demonstration','','pd.index'], ['Smart Sale','','ss.index']
		],
		'Tickets'	=>	[
			['Create ticket','','tkt.create'], ['New Tickets','','tkt.new'], ['Opened Tickets','','tkt.opened'], 'divider',
			['Active tickets','','tkt.index'], ['In Progress Tickets','','tkt.inprogress'], ['Holded Tickets','','tkt.holded'], 'divider',
			['Closed Tickets','','tkt.closed'], ['Completed tickets','','tkt.completed'], 'divider',
            ['All Tasks','','tsk.index'], ['New Tasks','','tsk.new'], ['Working Tasks','','tsk.working'], ['Holded Tasks','','tsk.holded'], ['Closed tasks','','tsk.closed'],
		],
	],
	'distributor'	=>	[
		['Customers','','customer.index'], ['Dealers','dealer'],	'Product'	=>	[
			['Information','','product.information']
		], 'Support Tickets'	=>	[
			['Create Ticket','','tkt.create'], 'divider', ['Active Tickets','','tkt.index'], 'divider', ['Closed Tickets','','tkt.closed'], ['Completed tickets','','tkt.completed'],
		],
		['Database Backups','','dbb.index'], ['Service Requests','','sreq.index'],
        //['FAQ','','faq.list']
	],
	'dealer'	=>	[
		['Customers','','customer.index'], ['Product Information','','product.information'], 'Support Tickets'	=>	[
			['Create Ticket','','tkt.create'], 'divider', ['Active Tickets','','tkt.index'], 'divider', ['Closed Tickets','','tkt.closed'], ['Completed tickets','','tkt.completed']
		],
		['Database Backups','','dbb.index'], ['Service Requests','','sreq.index'],
        //['FAQ','','faq.list']
	],
	'scm'	=>	[
		'Product'	=>	[
			['Products','products'], ['Editions','editions'], ['Packages','packages'], ['Features','features']
		],
		'Package'	=>	[
			['View Latest Packages','package/latest'], ['Upload Package','package/upload'], ['Verify Package','verify'], ['Delete Package Record','pkg/delete'], ['Approve Package','approve'], ['Revert Package','revert']
		],
	],
	'webdeveloper'	=>	[
		['Roles','role'], ['Resources','resource'],/* ['Actions','action'],*/
		'Distributor Branding' =>	[
			['Index','','db.index'], ['Form','','db.new']
		],
	],
	'supportteam'	=>	[
		'Product'	=>	[
			['Products','products'], ['Editions','editions'], ['Packages','packages'], 'divider',
			['Features','features'], ['Interact','','stp.product.interact'], 'divider',
            ['FAQ Manage','','faq.index'], ['FAQ List','','faq.list']
		],
		'Package'	=>	[
			['View Latest Packages','package/latest'], ['Upload Package','package/upload'], ['Verify Package','verify'], ['Delete Package Record','pkg/delete'], ['Approve Package','approve'], ['Revert Package','revert']
		],
		'Partners'	=>	[
			['Distributors','','stp.distributors'], ['Customers','','stp.customers'],
		],
		'Support'	=>	[
			['Support Agents','','tsa.index'], ['Agent Departments','','tsa.department'], 'divider',
			['Customer Cookie','','tscc.index'], ['Remote Connections','','crc.index'], 'divider',
			['Customer Print Objects','','cpo.index'], ['Customer Print Object Previews','','cpo.previews'], ['Public Print Objects','','ppo.index'], 'divider',
			['General Uploads','','gu.index'], ['Database Backups','','dbb.index'], 'divider',
			['Service Requests','','sreq.index'], ['Third Party Applications','','tpa.index'],

		],
		'Misc'	=>	[
			['Public Articles','','pa.index'], ['Emails','','mail.index'],
		],
		'Tickets'	=>	[
			['Create ticket','','tkt.create'], ['New Tickets','','tkt.new'], ['Opened Tickets','','tkt.opened'], 'divider',
			['Active tickets','','tkt.index'], ['In Progress Tickets','','tkt.inprogress'], ['Holded Tickets','','tkt.holded'], 'divider',
			['Closed Tickets','','tkt.closed'], ['Completed tickets','','tkt.completed']
		],
		'Tasks'	=>	[
			['All Tasks','','tsk.index'], ['New Tasks','','tsk.new'], ['Working Tasks','','tsk.working'], ['Holded Tasks','','tsk.holded'], ['Closed tasks','','tsk.closed'],
		]
	],
	'supportagent'	=>	[
		'Product'	=>	[
			['Products','products'], ['Editions','editions'], ['Packages','packages'], 'divider',
            ['Features','features'], ['Interact','','stp.product.interact'], 'divider',
			['FAQ Manage','','faq.index'], ['FAQ List','','faq.list']
		],
		'Package'	=>	[
			['View Latest Packages','package/latest'], ['Upload Package','package/upload'], ['Verify Package','verify'], ['Delete Package Record','pkg/delete'], ['Approve Package','approve'], ['Revert Package','revert']
		],
		'Partners'	=>	[
			['Distributors','','stp.distributors'], ['Customers','','stp.customers'],
		],
		'Support'	=>	[
			['Customer Cookie','','tscc.index'], ['Remote Connections','','crc.index'], 'divider',
			['Customer Print Objects','','cpo.index'], ['Customer Print Object Previews','','cpo.previews'], ['Public Print Objects','','ppo.index'], 'divider',
			['General Uploads','','gu.index'], ['Database Backups','','dbb.index'], 'divider',
			['Service Requests','','sreq.index'], ['Third Party Applications','','tpa.index'],
		],
		'Misc'	=>	[
			['Public Articles','','pa.index'], ['Emails','','mail.index'],
		],
		'Tickets'	=>	[
			['Create ticket','','tkt.create'], ['New Tickets','','tkt.new'], ['Opened Tickets','','tkt.opened'], 'divider',
			['Active tickets','','tkt.index'], ['In Progress Tickets','','tkt.inprogress'], ['Holded Tickets','','tkt.holded'], 'divider',
			['Closed Tickets','','tkt.closed'], ['Completed tickets','','tkt.completed']
		],
		'Tasks'	=>	[
			['All Tasks','','tsk.index'], ['New Tasks','','tsk.new'], ['Working Tasks','','tsk.working'], ['Holded Tasks','','tsk.holded'], ['Closed tasks','','tsk.closed'],
		]
	],
	'customer'	=>	[
		'Support Tickets'	=>	[
			['Create Ticket','','tkt.create'], 'divider',
			['Active Tickets','','tkt.index'], 'divider',
			['Closed Tickets','','tkt.closed'], ['Completed tickets','','tkt.completed']
		],
		['Print Objects','','cpo.index'], ['Database Backups','','dbb.index'],
        //['FAQ','','faq.list']
	],
]

?>
		<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="dashboard">MILESTONE</a>
		</div>
		<div id="navbar" class="collapse navbar-collapse">
			<ul class="nav navbar-nav">
				@if(array_key_exists(session("_rolename"),$_MENU))
				@foreach($_MENU[session("_rolename")] as $Parent => $SubMenu)
				@if(is_numeric($Parent) || $Parent == '')
					{!! _NAVLINK($SubMenu) !!}
				@else
					{!! _NAVDROPDOWNLI($Parent, $SubMenu) !!}
				@endif
				@endforeach
				@endif
			</ul>
			<div class="pull-right">
				<ul class="nav navbar-nav navbar-right">
					@if(Auth::check())
					<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ Auth::user()->partner()->pluck("name")[0] }}<!-- <small>{{ (session("_role")?("(" . App\Models\Role::where("code",session("_role"))->pluck("displayname")[0] . ")"):'') }}</small>--><span class="caret"></span></a>
						<ul class="dropdown-menu nav">
							<li class="nav-link"><a href="{{ Route('dashboard') }}">Dashboard</a></li>
							<li class="nav-link"><a href="{{ Route('pra.list') }}">Company Articles</a></li>
							<li class="nav-link"><a href="{{ Route('notification.list') }}">Notifications</a></li>
							<li class="nav-link"><a href="{{ Route('changepassword') }}">Change Password</a></li>
							<li class="nav-link"><a href="{{ Route('changeaddress') }}">Change Address</a></li>
							{!! (session("_roles")>1)?'<li class="nav-link"><a href="roleselect">Change Role</a></li>':'' !!}
							<li class="nav-link"><a href="{{ Route('home') }}">Site Home</a></li>
<li class="nav-link"><a href="logout">Logout</a></li>
						</ul>
					</li>
					@else
					<li class="nav-link"><a href="login">Login</a></li>
					@endif
				</ul>
			</div>
		</div>
	</div>
<?php
	function _NAVLINK($Ary){
		if($Ary == 'divider' || !is_array($Ary)) return '<li class="divider"></li>';
		$N = $Ary[0]; $H = ($Ary[1])?:Route($Ary[2]);
		return '<li class="nav-link"><a href="'.$H.'">'.$N.'</a></li>';
	}

	function _NAVDROPDOWNUL($SMAry){
		$LIs = '';
		foreach($SMAry as $Parent => $Ary){
			if(is_numeric($Parent)) $LIs .= _NAVLINK($Ary);
			else $LIs .= _NAVDROPDOWNLI($Parent,$Ary);
		}
		return '<ul class="dropdown-menu">'.$LIs.'</ul>';
	}

	function _NAVPARENTA($N){
		if($N == '' || empty($N)) return 'IM emprt';
		return '<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">'.$N.'<span class="caret"></span></a>';
	}

	function _NAVDROPDOWNLI($P,$S){
		return '<li class="dropdown">'.(_NAVPARENTA($P)).(_NAVDROPDOWNUL($S)).'</li>';
	}
?>