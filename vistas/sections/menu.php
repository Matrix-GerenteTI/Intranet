<div class="page-container row-fluid">
	<div class="page-sidebar ">


		<!-- MAIN MENU - START -->
		<div class="page-sidebar-wrapper" id="main-menu-wrapper"> 

			<!-- USER INFO - START -->
			<div class="profile-info row">

				<div class="profile-image col-md-4 col-sm-4 col-xs-4">
					<a href="ui-profile.html">
						<img src="/intranet/data/profile/profile.png" class="img-responsive img-circle">
					</a>
				</div>

				<div class="profile-details col-md-8 col-sm-8 col-xs-8">

					<h3>
						<a href="ui-profile.html"><?php echo $_SESSION['nombre']; ?></a>

						<!-- Available statuses: online, idle, busy, away and offline -->
						<span class="profile-status online"></span>
					</h3>

					<p class="profile-title"><?php echo $_SESSION['nivelT']; ?></p>

				</div>

			</div>
			<!-- USER INFO - END -->



			<ul class='wraplist'>	


				<li class="<?php echo $pag=='panel'?'open':''; ?>"> 
					<a href="/intranet/index.php?dashboard">
						<i class="fa fa-dashboard"></i>
						<span class="title">Tablero</span>
					</a>
				</li>
				<li class="<?php echo strpos(strtoupper($pag),'SGC_')===false?'':'open'; ?>">					
					<a href="javascript:;">
						<i class="fa fa-sitemap"></i>
						<span class="title">SGC</span>
						<span class="arrow "></span>
					</a>
					<ul class="sub-menu" >	
						<li>
							<a href="/intranet/index.php?view=SGC_recursos">Recursos</a>
						</li>
						<?php if($_SESSION['nivel']=='ADMINISTRADOR'){ ?>
						<li>
							<a href="/intranet/index.php?sgc_principal">Organigramas</a>
						</li>
						<?php } ?>
					</ul>	
				</li>
				<li class="<?php echo strpos($pag,'Direccion_')===false?'':'open'; ?>">					
					<a href="javascript:;">
						<i class="fa fa-sitemap"></i>
						<span class="title">Direcci&oacute;n</span>
						<span class="arrow "></span>
					</a>
					<ul class="sub-menu" >	
						<li>
							<a href="/intranet/index.php?view=Direccion_recursos">Recursos</a>
						</li>
					</ul>	
				</li>
				<li class="<?php echo substr($pag,0,4)=='con_'?'open':''; ?>"> 
					<a href="javascript:;">
						<i class="fa fa-calculator"></i>
						<span class="title">Contabilidad</span>
						<span class="arrow "></span>
					</a>
					<ul class="sub-menu" >	
						<!--
						<li class="<?php echo $pag=='con_cuentas'?'open':''; ?>">
							<a  href="/intranet/index.php?con_cuentas">Cuentas</a>
						</li>
						<li class="<?php echo $pag=='con_ingresos'?'open':''; ?>">
							<a href="/intranet/index.php?con_ingresos">Ingresos</a>
						</li>
						-->
						<li>
							<a href="/intranet/index.php?view=Administrativo_recursos">Recursos Administrativos</a>
						</li>							
						<li class="<?php echo $pag=='con_egresos'?'open':''; ?>">
							<a href="/intranet/index.php?con_egresos">Gastos</a>
						</li>
						<li>
							<a href="/intranet/contabilidad/programacion">Programación Pagos</a>
						</li>						
						<?php if($_SESSION['nivel']=='1'){ ?>
						<li>
							<a class="<?php echo $pag=='con_edosfinancieros'?'open':''; ?>" href="/intranet/index.php?con_edosfinancieros">Estados Financieros</a>
                        </li>
                        <li>
                        <a href="/intranet/contabilidad/facturacion">Facturación global</a>
                        </li>
						<li>
							<a  href="http://servermatrixxxb.ddns.net:8181/intranet/controladores/reportes/cxp/deudasProveedores.php">CXP</a>
						</li>						
						<?php } ?>
						<li>
							<a href="/intranet/index.php?con_activos">Activos Fijos</a>
                        </li>
                        <?php if( $_SESSION['nivel'] == '1' ): ?>
						<li>
							<a href="/intranet/contabilidad/facturacion">Facturaci&oacute;n Grupal</a>
						</li>	                            
                        <?php endif ?>
					</ul>
				</li>
				<!-- New -->
				<li class="<?php echo substr($pag,0,4)=='per_'?'open':''; ?>"> 
					<a href="javascript:;">
						<i class="fa fa-group"></i>
						<span class="title">RH</span>
						<span class="arrow "></span>
					</a>
					<ul class="sub-menu" >	
						<li>
							<a href="/intranet/index.php?view=RH_recursos">Recursos</a>
						</li>		
						<li> 
							<a href="/intranet/index.php?per_acorrectiva">Acciones Correctivas</a>
						</li>
						<li class="">
							<a  href="/intranet/trabajadores/fotografiar">Trabajadores</a>
						</li>			
						<?php if($_SESSION['nivel']=='1' || $_SESSION['usuario']=='AJSA'){ ?>					
						<li class="<?php echo $pag=='per_socioeconomico'?'open':''; ?>">
							<a  href="/intranet/index.php?per_socioeconomico">Evidencias Socioeconómico</a>
						</li>																			
						<li class="<?php echo $pag=='per_kpis'?'open':''; ?>">
							<a  href="/intranet/index.php?per_kpi">Indicador Clave de Rendimiento (PRUEBAS)</a>
						</li>
						<!--
						<li class="<?php //echo $pag=='per_areas'?'open':''; ?>">
							<a href="/intranet/index.php?per_areas">Areas y Puestos</a>
						</li>-->
						<li class="<?php echo $pag=='per_tareas'?'open':''; ?>">
							<a href="/intranet/index.php?per_tareas">Tareas</a>
						</li>
						<li class="<?php echo $pag=='recursos_materiales'?'open':''; ?>">
							<a href="/intranet/requisicion/surtir">Recursos Materiales</a>
						</li>
						<li class="<?php echo $pag=='resguardos'?'open':''; ?>">
							<a href="/intranet/index.php?resguardos">Resguardos</a>
						</li>
					<?php }else if($_SESSION['usuario']=='sleon') {?>
						<li class="<?php echo $pag=='recursos_materiales'?'open':''; ?>">
							<a href="/intranet/requisicion/surtir">Recursos Materiales</a>
						</li>
					<?php }?>
					</ul>
				</li>			
				<li class="<?php echo strpos($pag,'ventas_')===false?'':'open'; ?>">
					<a href="javascript:;">
						<i class="fa fa-shopping-bag"></i>
						<span class="title">Ventas</span>
						<span class="arrow "></span>
					</a>
					<ul class="sub-menu" >	
						<li>
							<a href="/intranet/index.php?view=ventas_recursos">Recursos</a>
						</li>
						<li>
							<a href="/intranet/index.php?view=precios_recursos">Precios</a>
						</li>						
						<li>
							<a href="/intranet/index.php?ventas_clasificador">Catalogo Suc.</a>
						</li>
						<li>
							<a href="/intranet/index.php?aleatorios_excepciones">Inventarios aleatorios (Excepciones)</a>
						</li>
					</ul>					
				</li>			
				<li class="<?php echo strpos($pag,'compras_')===false?'':'open'; ?>">
				<a href="javascript:;">
						<i class="fa fa-truck"></i>
						<span class="title">Compras</span>
						<span class="arrow "></span>
					</a>
					<ul class="sub-menu" >	
						<li>
							<a href="/intranet/index.php?view=compras_recursos">Recursos</a>
						</li>
					</ul>							
				</li>		
				<li class="<?php echo strpos($pag,'almacen_')===false?'':'open'; ?>">
					<a href="javascript:;">
						<i class="fa fa-cubes"></i>
						<span class="title">Almacén</span>
						<span class="arrow "></span>
					</a>
					<ul class="sub-menu" >	
						<li>
							<a href="/intranet/index.php?view=almacen_recursos">Recursos</a>
						</li>
						<li>
							<a href="/intranet/almacen/v/traspasos/">Traspasos</a>
						</li>   						
					</ul>					
				</li>	
				<?php if($_SESSION['usuario'] == "lflores" || $_SESSION['usuario'] == "admin" || $_SESSION['usuario'] == "sleon" || $_SESSION['usuario'] == "AJSA"){?>
					<li class="<?php echo strpos($pag,'precios_')===false?'':'open'; ?>">
						<a href="/intranet/index.php?precios_productos">
							<i class="fa fa-money"></i>
							<span class="title">Precios x productos</span>
							
						</a>
					</li>
				<?php }?>
				<li class="<?php echo strpos($pag,'ti_')===false?'':'open'; ?>">
					<a href="javascript:;">
						<i class="fa fa-laptop"></i>
						<span class="title">TI</span>
						<span class="arrow "></span>
					</a>
					<ul class="sub-menu" >	
						<li>
							<a href="/intranet/index.php?view=ti_recursos">Recursos</a>
						</li>
					</ul>							
				</li>
			</ul>

		</div>
		<!-- MAIN MENU - END -->



		<div class="project-info">

			<div class="block1">
				<div class="data">
					<span class='title'>&copy; Matrix Intranet. Todos los derechos reservados.</span>
				</div>
				<!--
				<div class="graph">
					<span class="sidebar_orders">...</span>
				</div>
				-->
			</div>
			<!--
			<div class="block2">
				<div class="data">
					<span class='title'>Visitors</span>
					<span class='total'>345</span>
				</div>
				<div class="graph">
					<span class="sidebar_visitors">...</span>
				</div>
			</div>
			-->
		</div>



	</div>
	<section id="main-content" class=" ">
		<section class="wrapper" style='margin-top:60px;display:inline-block;width:100%;padding:15px 0 0 15px;'>