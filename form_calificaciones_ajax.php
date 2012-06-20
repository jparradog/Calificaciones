<?PHP
	// connect to the database
	ini_set("memory_limit", "256M"); 
	include "../php/clase/application.php";
	$mostrarError=true;
	//include "../php/clase/function.php";
	$obj = new DB_mysql();
	$obj->conectar($bd, $host, $user, $pass, $log);
	$consulta="
	SELECT cod_dane 
	FROM datos_colegio 
	WHERE sede='1'
	";
	$obj->consulta($consulta, $mostrarError);
	if($obj->numregistros()<1)//if(1)
		{
			//no hay configuracion
		}
	else if($obj->numregistros()==1)//if(1)
		{
			$col=$obj->result_array_asoc();
			$colegio_1=$col[0]['cod_dane'];
		}
	//$obj->mensaje($_SESSION['user'].$_SESSION['user_valied'].$_SESSION['colegio']);
	if(!$_SESSION['user'] 
		|| !$_SESSION['user_valied']
		|| !$_SESSION['colegio']  
		|| !strstr($_SESSION['colegio'], $colegio_1) ){ // || strstr($_SESSION['user_valied'], "6") || strstr($_SESSION['user_valied'], "4")

       $b="reporte_matriculados_sexo/form_matriculado_ajax.php";
		$a=str_replace($b, '',$_SERVER["HTTP_HOST"].$_SERVER['REQUEST_URI']);
		header ("location: http://".$a); 
        //$obj->saltar_pagina("../index.php");
        $obj->mensaje('Debe iniciar sesion');
        exit();
    }
    $datos=$obj->limpiar_todo($_POST);
    $peto="form1-";
    //echo "hola";
    if ($datos[$peto."opcion"]==1 && $datos[$peto."docente"] && $datos[$peto."year"]) {
    	$consulta="
			SELECT p.pensum, m.nombre, c.grado, c.curso, p.activo 
FROM pensums AS p 
INNER JOIN cursos AS c ON p.curso=c.id_curso  
INNER JOIN materias AS m ON p.materia=m.materia
WHERE c.years = '".$obj->html_decode($datos[$peto."year"])."' 
AND p.docente = '".$obj->html_decode($datos[$peto."docente"])."' 
AND p.activo!='N' 
ORDER BY c.grado, c.curso
		";
		$obj->consulta($consulta, $mostrarError);
		//$obj->verconsulta();
		if($obj->numregistros()<1)//if(1)
			{
				echo '1';//Error 105:no hay pensum
			}
		else
			{
				$result=$obj->result_array_asoc();
				$html='';
				for ($i=0; $i < $obj->numregistros(); $i++) { 
					$html.='
						<option value="'.$obj->html_encode($result[$i]['pensum']).'" data-activo="'.$obj->html_encode($result[$i]['activo']).'">('.$obj->html_encode($result[$i]['grado'].'-'.$result[$i]['curso'].') '.$result[$i]['nombre']).'</option>
					';
				}
				echo $html;
			}
    } else if ($datos[$peto."opcion"]==2 && $datos[$peto."year"]) {
    	$consulta="
    		SELECT d.cedula, d.d_apellido1 AS a1, d.d_apellido2 AS a2, d.d_nombre1 AS n1, d.d_nombre2 AS n2, d.foto
FROM `docentes` AS d
INNER JOIN `pensums` AS p ON d.cedula = p.docente
INNER JOIN cursos AS c ON p.curso = c.id_curso
WHERE p.activo != 'N'
AND c.years ='".$obj->html_decode($datos[$peto."year"])."'
GROUP BY d.cedula
ORDER BY d.d_apellido1, d.d_apellido2, d.d_nombre1, d.d_nombre2
		";
		$obj->consulta($consulta, $mostrarError);
		//$obj->verconsulta();
		if($obj->numregistros()<1)//if(1)
			{
				echo '1';//Error 106:No hay Docentes con pensum asignado para este año
			}
		else
			{
				$result=$obj->result_array_asoc();
				$html='';
				for ($a=0; $a < $obj->numregistros(); $a++) { 
					$html.='<option data-foto="'.$result[$a]['foto'].'" value="'.$result[$a]['cedula'].'">'.$obj->html_encode($result[$a]['a1']." ".$result[$a]['a2']." ".$result[$a]['n1']." ".$result[$a]['n2']." (".$result[$a]['cedula'])." )".'</option>';
				}
				echo $html;
			}
    } else if ($datos[$peto."opcion"]==3 && $datos[$peto."year"]) {
    	$consulta="
    		SELECT y.id_year, c.pv 
FROM `configura` AS c 
INNER JOIN years AS y ON c.year_v=y.year 
    	";
    	$obj->consulta($consulta, $mostrarError);
		//$obj->verconsulta();
		if($obj->numregistros()<1)//if(1)
			{
				echo '2';//Error 108:No hay Configura
			}
		else if($obj->numregistros()==1)
			{
				$ok=0;
				$configura=$obj->result_array_asoc();
				if ($configura[0]['id_year']!=$datos[$peto."year"]) {
					$consulta="
					SELECT p.periodo
FROM periodos AS p 
WHERE p.year='".$obj->html_decode($datos[$peto."year"])."'
					";
					$ok=1;
				} else if($configura[0]['id_year']==$datos[$peto."year"]) {
					$consulta="
					SELECT p.periodo
FROM periodos AS p 
WHERE p.year=".$obj->html_decode($datos[$peto."year"])." AND p.periodo <=".$configura[0]['pv']."
					";
					$ok=1;
					//echo "<script> alert(".$consulta."); </script>";
				}
				$obj->consulta($consulta, $mostrarError);
				if($ok==1)
					{
						if($obj->numregistros()<1)//if(1)
							{
								echo '1';//Error 107:No hay Periodos creados para este año
							}
						else
							{
								$result=$obj->result_array_asoc();
								$html='';
								for ($a=0; $a < $obj->numregistros(); $a++) { 
									echo '<option value="'.$result[$a]['periodo'].'">'.$obj->html_encode($result[$a]['periodo']).'</option>';
								}
								echo '<option value="5">Final</option>';
							}
					}				
			}
    } else if ($datos[$peto."opcion"]==4 && $datos[$peto."year"] && $datos[$peto."docente"] && $datos[$peto."periodo"] && $datos[$peto."pensum"]) {
    	//echo $datos[$peto."year"]."<br>".$datos[$peto."docente"]."<br>".$datos[$peto."periodo"]."<br>".$datos[$peto."pensum"];
    	$consulta="
    		SELECT p.pensum, p.duplicafm, p.curso, p.materia, p.l1, p.l2, p.l3, p.l4, p.l5, p.l6, p.l7, p.l8, p.l9 	l10, p.l11, p.l12 
FROM `pensums` AS p
WHERE `pensum` =".$datos[$peto."pensum"]."
    	";
    	$obj->consulta($consulta, $mostrarError);
    	if($obj->numregistros()<1){
			echo '1';//Error 109:No existe el pensum 
		} else if($obj->numregistros()>1){
			echo '2';//Error 110:Existe mas de un pensum con el mismo id
		} else if($obj->numregistros()==1){
			$pensum=$obj->result_array_asoc();
			if($pensum[0]['duplicafm']=="N" || $pensum[0]['duplicafm']=="n"){
				//utiliza duplicaf
				$consulta="
				SELECT d.curso_o 
FROM `duplicaf` AS d
WHERE d.`curso_d` LIKE '".$pensum[0]['curso']."'
				";
				$obj->consulta($consulta, $mostrarError);
				if($obj->numregistros()<1){
					echo '3';//Error 111:No existe el curso origen
				} else if($obj->numregistros()>1){
					echo '4';//Error 112:Existe mas de un curso origen
				} else if($obj->numregistros()==1){
					$origen=$obj->result_array_asoc();
					$consulta="
						SELECT f.in, f.id_frase, f.frase, f.periodo
FROM `frases` AS f
WHERE f.`curso` LIKE '".$origen[0]['curso_o']."'
AND f.`tipo` LIKE 'L'
AND f.`materia` LIKE '".$pensum[0]['materia']."'
ORDER BY f.`in` ASC
					";
					$obj->consulta($consulta, $mostrarError);
					if($obj->numregistros()<1){
						echo '5';//Error 113:No existen frases
					} else if($obj->numregistros()>=1){
						$frases=$obj->result_array_asoc();
						//$obj->verconsulta();
						$html='
							<thead>
							    <tr>
							      	<th colspan="4">Logros</th>
							    </tr>
							    <tr>
							      	<th>#</th>
							      	<th>Codigo</th>
							      	<th>Descripción</th>
							      	<th>Periodo</th>
							    </tr>
							</thead>
							<tbody>
						';
						$cant=$obj->numregistros();
						$consulta="
    						SELECT y.id_year, c.pv 
FROM `configura` AS c 
INNER JOIN years AS y ON c.year_v=y.year 
				    	";
				    	$obj->consulta($consulta, $mostrarError);
						//$obj->verconsulta();
						if($obj->numregistros()<1)//if(1)
							{
								echo '6';//Error 108:No hay Configura
							}
						else if($obj->numregistros()==1)
							{
								$configura=$obj->result_array_asoc();
								$consulta="
								SELECT p.periodo
FROM periodos AS p 
WHERE p.year='".$obj->html_decode($datos[$peto."year"])."'
								";
								$obj->consulta($consulta, $mostrarError);
								if($obj->numregistros()<1)//if(1)
									{
										echo '7';//Error 107:No hay Periodos creados para este año
									}
								else
									{
										$result=$obj->result_array_asoc();
										$num_result=$obj->numregistros();
									}
								for ($i=0; $i < $cant; $i++) {
									$html.='
										<tr>
									      	<td>'.$frases[$i]['in'].'</td>
									      	<td>'.$frases[$i]['id_frase'].'</td>
									      	<td>'.$obj->html_encode($frases[$i]['frase']).'</td>
									    ';
									if ($configura[0]["id_year"]==$datos[$peto."year"]) {
										if ($configura[0]["pv"]==$datos[$peto."periodo"]) {
											if ($pensum[0]['L'.$frases[$i]['in']]=='0' || $pensum[0]['L'.$frases[$i]['in']]>$datos[$peto."periodo"]) {
												$html.='
													<td>
														<select class="span3">
										        			<option value="'.$frases[$i]['periodo'].'" seleted>'.$frases[$i]['periodo'].'</option>
															<option value="'.$datos[$peto."periodo"].'">'.$datos[$peto."periodo"].'</option>
										        		</select>
										        	</td>
												';
											}
											else if($pensum[0]['L'.$frases[$i]['in']]>'0' && $pensum[0]['L'.$frases[$i]['in']]<$datos[$peto."periodo"]){
												$html.='
													<td>
														<select class="span3">
										        			<option value="'.$frases[$i]['periodo'].'" seleted>'.$frases[$i]['periodo'].'</option>
										        		</select>
										        	</td>
												';
											}
										}
									} else if ($configura[0]["id_year"]>$datos[$peto."year"]) {
										$html.='
											<td>
												<select class="span1">
										';
										//echo "k:".$pensum[0]['l'.$frases[$i]['in']];
										if(intval($pensum[0]['l'.$frases[$i]['in']])==0){
											$html.='
											<option value="0" seleted>0</option>
											';
											for ($j=0; $j < $num_result; $j++) { 
												$html.='
												<option value="'.$result[$j]['periodo'].'">'.$result[$j]['periodo'].'</option>
												';
											}
										}else if(intval($pensum[0]['l'.$frases[$i]['in']])>0){
											$ht="";
											$ht2="";
											for ($j=0; $j < $num_result; $j++) { 
												//echo "(".$pensum[0]['l'.$frases[$i]['in']]." - ".$result[$j]['periodo'].")";
												if ($pensum[0]['l'.$frases[$i]['in']]==$result[$j]['periodo']) {
													$ht2='
													<option value="'.$result[$j]['periodo'].'" seleted>'.$result[$j]['periodo'].'</option>
													';
												}
												else{
													$ht.='
													<option value="'.$result[$j]['periodo'].'">'.$result[$j]['periodo'].'</option>
													';
												}
											}
											$html.=$ht2.$ht.'
													<option value="0">0</option>
											';
										}
										$html.='
								        		</select>
								        	</td>
										';
									}
									$html.='
									    </tr>
									';
								}
							}
						$html.='
							</tbody>
						';
						echo $html;
					}
				}
			} else if($pensum[0]['duplicafm']=="S" || $pensum[0]['duplicafm']=="s"){
				//utiliza duplicafm
			}
		}
    }
?>