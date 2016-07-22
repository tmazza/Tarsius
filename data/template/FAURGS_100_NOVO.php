<?php
// $ajusteX = 0;
$ajusteX = 0;
// $ajusteY = 0;
$ajusteY = 0;

$a1x = 0;
$a1y = 0;
$distAncHor = 187;
$distAncVer = 274;
// 11.811023622047
# em milimetros
return array(
	'raioTriangulo' => (14 * sqrt(2)) / 2, # diagonal / 2

	'ancora1' => array($a1x,$a1y),

	'distAncHor' => $distAncHor,
	'distAncVer' => $distAncVer,
	# devem ser procisas
	'disElipHor' => 4.75,
	'disElipVer' => 71.55,

	'elpAltura' => 2.5,
  'elpLargura' => 4.36,


	'regioes' => array( # distancias relativas a ancora 1
			array(0,-181.69466666667,-59.605333333333,'A','W'),
			array(0,-172.974,-59.605333333333,'B','W'),
			array(0,-164.25333333333,-59.605333333333,'C','W'),
			array(0,32.935333333333,144.94933333333,'D','W'),
			array(0,-146.89666666667,-59.605333333333,'E','W'),

			array(0,-181.69466666667,-55.626,'A','W'),
			array(0,-172.974,-55.626,'B','W'),
			array(0,-164.25333333333,-55.626,'C','W'),
			array(0,32.935333333333,160.95133333333,'D','W'),
			array(0,-146.89666666667,-55.626,'E','W'),

			array(0,-181.69466666667,-51.562,'A','W'),
			array(0,-172.974,-51.562,'B','W'),
			array(0,-164.25333333333,-51.562,'C','W'),
			array(0,32.935333333333,216.916,'D','W'),
			array(0,41.656,144.94933333333,'E','W'),

			array(0,6.858,140.97,'A','W'),
			array(0,15.578666666667,140.97,'B','W'),
			array(0,24.299333333333,140.97,'C','W'),
			array(0,-155.53266666667,-59.605333333333,'D','W'),
			array(0,41.656,148.92866666667,'E','W'),

			array(0,6.858,144.94933333333,'A','W'),
			array(0,15.578666666667,144.94933333333,'B','W'),
			array(0,24.299333333333,144.94933333333,'C','W'),
			array(0,-155.53266666667,-55.626,'D','W'),
			array(0,41.656,164.93066666667,'E','W'),

			array(0,6.858,148.92866666667,'A','W'),
			array(0,15.578666666667,148.92866666667,'B','W'),
			array(0,24.299333333333,148.92866666667,'C','W'),
			array(0,-155.53266666667,-51.562,'D','W'),
			array(0,41.656,168.91,'E','W'),

			array(0,6.858,152.908,'A','W'),
			array(0,15.578666666667,152.908,'B','W'),
			array(0,24.299333333333,152.908,'C','W'),
			array(0,33.02,140.97,'D','W'),
			array(0,41.656,180.93266666667,'E','W'),

			array(0,6.858,156.972,'A','W'),
			array(0,15.578666666667,156.972,'B','W'),
			array(0,24.299333333333,156.972,'C','W'),
			array(0,33.02,148.92866666667,'D','W'),
			array(0,41.656,184.912,'E','W'),

			array(0,6.858,160.95133333333,'A','W'),
			array(0,15.578666666667,160.95133333333,'B','W'),
			array(0,24.299333333333,160.95133333333,'C','W'),
			array(0,33.02,152.908,'D','W'),
			array(0,41.656,188.89133333333,'E','W'),

			array(0,6.858,164.93066666667,'A','W'),
			array(0,15.578666666667,164.93066666667,'B','W'),
			array(0,24.299333333333,164.93066666667,'C','W'),
			array(0,33.02,156.972,'D','W'),
			array(0,41.656,196.93466666667,'E','W'),

			array(0,6.858,168.91,'A','W'),
			array(0,15.578666666667,168.91,'B','W'),
			array(0,24.299333333333,168.91,'C','W'),
			array(0,33.02,164.93066666667,'D','W'),
			array(0,41.656,200.914,'E','W'),

			array(0,6.858,172.974,'A','W'),
			array(0,15.578666666667,172.974,'B','W'),
			array(0,24.299333333333,172.974,'C','W'),
			array(0,33.02,168.91,'D','W'),
			array(0,41.656,204.89333333333,'E','W'),

			array(0,6.858,176.95333333333,'A','W'),
			array(0,15.578666666667,176.95333333333,'B','W'),
			array(0,24.299333333333,176.95333333333,'C','W'),
			array(0,33.02,172.974,'D','W'),
			array(0,41.656,220.89533333333,'E','W'),

			array(0,6.858,180.93266666667,'A','W'),
			array(0,15.578666666667,180.93266666667,'B','W'),
			array(0,24.299333333333,180.93266666667,'C','W'),
			array(0,33.02,176.95333333333,'D','W'),
			array(0,41.656,224.87466666667,'E','W'),

			array(0,6.858,184.912,'A','W'),
			array(0,15.578666666667,184.912,'B','W'),
			array(0,24.299333333333,184.912,'C','W'),
			array(0,33.02,180.93266666667,'D','W'),
			array(0,-146.812,-51.562,'E','W'),

			array(0,6.858,188.89133333333,'A','W'),
			array(0,15.578666666667,188.89133333333,'B','W'),
			array(0,24.299333333333,188.89133333333,'C','W'),
			array(0,33.02,184.912,'D','W'),
			array(0,41.740666666667,140.97,'E','W'),

			array(0,6.858,192.95533333333,'A','W'),
			array(0,15.578666666667,192.95533333333,'B','W'),
			array(0,24.299333333333,192.95533333333,'C','W'),
			array(0,33.02,188.89133333333,'D','W'),
			array(0,41.740666666667,152.908,'E','W'),

			array(0,6.858,196.93466666667,'A','W'),
			array(0,15.578666666667,196.93466666667,'B','W'),
			array(0,24.299333333333,196.93466666667,'C','W'),
			array(0,33.02,192.95533333333,'D','W'),
			array(0,41.740666666667,156.972,'E','W'),

			array(0,6.858,200.914,'A','W'),
			array(0,15.578666666667,200.914,'B','W'),
			array(0,24.299333333333,200.914,'C','W'),
			array(0,33.02,196.93466666667,'D','W'),
			array(0,41.740666666667,160.95133333333,'E','W'),

			array(0,6.858,204.89333333333,'A','W'),
			array(0,15.578666666667,204.89333333333,'B','W'),
			array(0,24.299333333333,204.89333333333,'C','W'),
			array(0,33.02,200.914,'D','W'),
			array(0,41.740666666667,172.974,'E','W'),

			array(0,6.858,208.95733333333,'A','W'),
			array(0,15.578666666667,208.95733333333,'B','W'),
			array(0,24.299333333333,208.95733333333,'C','W'),
			array(0,33.02,204.89333333333,'D','W'),
			array(0,41.740666666667,176.95333333333,'E','W'),

			array(0,6.858,212.93666666667,'A','W'),
			array(0,15.578666666667,212.93666666667,'B','W'),
			array(0,24.299333333333,212.93666666667,'C','W'),
			array(0,33.02,208.95733333333,'D','W'),
			array(0,41.740666666667,192.95533333333,'E','W'),

			array(0,6.858,216.916,'A','W'),
			array(0,15.578666666667,216.916,'B','W'),
			array(0,24.299333333333,216.916,'C','W'),
			array(0,33.02,212.93666666667,'D','W'),
			array(0,41.740666666667,208.95733333333,'E','W'),

			array(0,6.858,220.89533333333,'A','W'),
			array(0,15.578666666667,220.89533333333,'B','W'),
			array(0,24.299333333333,220.89533333333,'C','W'),
			array(0,33.02,220.89533333333,'D','W'),
			array(0,41.740666666667,212.93666666667,'E','W'),

			array(0,6.858,224.87466666667,'A','W'),
			array(0,15.578666666667,224.87466666667,'B','W'),
			array(0,24.299333333333,224.87466666667,'C','W'),
			array(0,33.02,224.87466666667,'D','W'),
			array(0,41.740666666667,216.916,'E','W'),

			array(0,-133.77333333333,-59.605333333333,'A','W'),
			array(0,-125.05266666667,-59.605333333333,'B','W'),
			array(0,-116.332,-59.605333333333,'C','W'),
			array(0,-107.61133333333,-59.605333333333,'D','W'),
			array(0,-98.890666666667,-59.605333333333,'E','W'),

			array(0,-133.77333333333,-55.626,'A','W'),
			array(0,-125.05266666667,-55.626,'B','W'),
			array(0,-116.332,-55.626,'C','W'),
			array(0,-107.61133333333,-55.626,'D','W'),
			array(0,-98.890666666667,-55.626,'E','W'),

			array(0,-133.77333333333,-51.562,'A','W'),
			array(0,-125.05266666667,-51.562,'B','W'),
			array(0,-116.332,-51.562,'C','W'),
			array(0,-107.61133333333,-51.562,'D','W'),
			array(0,-98.890666666667,-51.562,'E','W'),

			array(0,54.779333333333,140.97,'A','W'),
			array(0,63.5,140.97,'B','W'),
			array(0,72.220666666667,140.97,'C','W'),
			array(0,80.941333333333,140.97,'D','W'),
			array(0,89.662,140.97,'E','W'),

			array(0,54.779333333333,144.94933333333,'A','W'),
			array(0,63.5,144.94933333333,'B','W'),
			array(0,72.220666666667,144.94933333333,'C','W'),
			array(0,80.941333333333,144.94933333333,'D','W'),
			array(0,89.662,144.94933333333,'E','W'),

			array(0,54.779333333333,148.92866666667,'A','W'),
			array(0,63.5,148.92866666667,'B','W'),
			array(0,72.220666666667,148.92866666667,'C','W'),
			array(0,80.941333333333,148.92866666667,'D','W'),
			array(0,89.662,148.92866666667,'E','W'),

			array(0,54.779333333333,152.908,'A','W'),
			array(0,63.5,152.908,'B','W'),
			array(0,72.220666666667,152.908,'C','W'),
			array(0,80.941333333333,152.908,'D','W'),
			array(0,89.662,152.908,'E','W'),

			array(0,54.779333333333,156.972,'A','W'),
			array(0,63.5,156.972,'B','W'),
			array(0,72.220666666667,156.972,'C','W'),
			array(0,80.941333333333,156.972,'D','W'),
			array(0,89.662,156.972,'E','W'),

			array(0,54.779333333333,160.95133333333,'A','W'),
			array(0,63.5,160.95133333333,'B','W'),
			array(0,72.220666666667,160.95133333333,'C','W'),
			array(0,80.941333333333,160.95133333333,'D','W'),
			array(0,89.662,160.95133333333,'E','W'),

			array(0,54.779333333333,164.93066666667,'A','W'),
			array(0,63.5,164.93066666667,'B','W'),
			array(0,72.220666666667,164.93066666667,'C','W'),
			array(0,80.941333333333,164.93066666667,'D','W'),
			array(0,89.662,164.93066666667,'E','W'),

			array(0,54.779333333333,168.91,'A','W'),
			array(0,63.5,168.91,'B','W'),
			array(0,72.220666666667,168.91,'C','W'),
			array(0,80.941333333333,168.91,'D','W'),
			array(0,89.662,168.91,'E','W'),

			array(0,54.779333333333,172.974,'A','W'),
			array(0,63.5,172.974,'B','W'),
			array(0,72.220666666667,172.974,'C','W'),
			array(0,80.941333333333,172.974,'D','W'),
			array(0,89.662,172.974,'E','W'),

			array(0,54.779333333333,176.95333333333,'A','W'),
			array(0,63.5,176.95333333333,'B','W'),
			array(0,72.220666666667,176.95333333333,'C','W'),
			array(0,80.941333333333,176.95333333333,'D','W'),
			array(0,89.662,176.95333333333,'E','W'),

			array(0,54.779333333333,180.93266666667,'A','W'),
			array(0,63.5,180.93266666667,'B','W'),
			array(0,72.220666666667,180.93266666667,'C','W'),
			array(0,80.941333333333,180.93266666667,'D','W'),
			array(0,89.662,180.93266666667,'E','W'),

			array(0,54.779333333333,184.912,'A','W'),
			array(0,63.5,184.912,'B','W'),
			array(0,72.220666666667,184.912,'C','W'),
			array(0,80.941333333333,184.912,'D','W'),
			array(0,89.662,184.912,'E','W'),

			array(0,54.779333333333,188.89133333333,'A','W'),
			array(0,63.5,188.89133333333,'B','W'),
			array(0,72.220666666667,188.89133333333,'C','W'),
			array(0,80.941333333333,188.89133333333,'D','W'),
			array(0,89.662,188.89133333333,'E','W'),

			array(0,54.779333333333,192.95533333333,'A','W'),
			array(0,63.5,192.95533333333,'B','W'),
			array(0,72.220666666667,192.95533333333,'C','W'),
			array(0,80.941333333333,192.95533333333,'D','W'),
			array(0,89.662,192.95533333333,'E','W'),

			array(0,54.779333333333,196.93466666667,'A','W'),
			array(0,63.5,196.93466666667,'B','W'),
			array(0,72.220666666667,196.93466666667,'C','W'),
			array(0,80.941333333333,196.93466666667,'D','W'),
			array(0,89.662,196.93466666667,'E','W'),

			array(0,54.779333333333,200.914,'A','W'),
			array(0,63.5,200.914,'B','W'),
			array(0,72.220666666667,200.914,'C','W'),
			array(0,80.941333333333,200.914,'D','W'),
			array(0,89.662,200.914,'E','W'),

			array(0,54.779333333333,204.89333333333,'A','W'),
			array(0,63.5,204.89333333333,'B','W'),
			array(0,72.220666666667,204.89333333333,'C','W'),
			array(0,80.941333333333,204.89333333333,'D','W'),
			array(0,89.662,204.89333333333,'E','W'),

			array(0,54.779333333333,208.95733333333,'A','W'),
			array(0,63.5,208.95733333333,'B','W'),
			array(0,72.220666666667,208.95733333333,'C','W'),
			array(0,80.941333333333,208.95733333333,'D','W'),
			array(0,89.662,208.95733333333,'E','W'),

			array(0,54.779333333333,212.93666666667,'A','W'),
			array(0,63.5,212.93666666667,'B','W'),
			array(0,72.220666666667,212.93666666667,'C','W'),
			array(0,80.941333333333,212.93666666667,'D','W'),
			array(0,89.662,212.93666666667,'E','W'),

			array(0,54.779333333333,216.916,'A','W'),
			array(0,63.5,216.916,'B','W'),
			array(0,72.220666666667,216.916,'C','W'),
			array(0,80.941333333333,216.916,'D','W'),
			array(0,89.662,216.916,'E','W'),

			array(0,54.779333333333,220.89533333333,'A','W'),
			array(0,63.5,220.89533333333,'B','W'),
			array(0,72.220666666667,220.89533333333,'C','W'),
			array(0,80.941333333333,220.89533333333,'D','W'),
			array(0,89.662,220.89533333333,'E','W'),

			array(0,54.779333333333,224.87466666667,'A','W'),
			array(0,63.5,224.87466666667,'B','W'),
			array(0,72.220666666667,224.87466666667,'C','W'),
			array(0,80.941333333333,224.87466666667,'D','W'),
			array(0,89.662,224.87466666667,'E','W'),

			array(0,-85.852,-59.605333333333,'A','W'),
			array(0,-77.131333333333,-59.605333333333,'B','W'),
			array(0,-68.410666666667,-59.605333333333,'C','W'),
			array(0,-59.69,-59.605333333333,'D','W'),
			array(0,-50.969333333333,-59.605333333333,'E','W'),

			array(0,-85.852,-55.626,'A','W'),
			array(0,-77.131333333333,-55.626,'B','W'),
			array(0,-68.410666666667,-55.626,'C','W'),
			array(0,-59.69,-55.626,'D','W'),
			array(0,-50.969333333333,-55.626,'E','W'),

			array(0,-85.852,-51.562,'A','W'),
			array(0,-77.131333333333,-51.562,'B','W'),
			array(0,-68.410666666667,-51.562,'C','W'),
			array(0,-59.69,-51.562,'D','W'),
			array(0,-50.969333333333,-51.562,'E','W'),

			array(0,101.092,139.36133333333,'A','W'),
			array(0,109.81266666667,139.36133333333,'B','W'),
			array(0,118.53333333333,139.36133333333,'C','W'),
			array(0,127.254,139.36133333333,'D','W'),
			array(0,135.97466666667,139.36133333333,'E','W'),

			array(0,101.092,143.34066666667,'A','W'),
			array(0,109.81266666667,143.34066666667,'B','W'),
			array(0,118.53333333333,143.34066666667,'C','W'),
			array(0,127.254,143.34066666667,'D','W'),
			array(0,135.97466666667,143.34066666667,'E','W'),

			array(0,101.092,147.32,'A','W'),
			array(0,109.81266666667,147.32,'B','W'),
			array(0,118.53333333333,147.32,'C','W'),
			array(0,127.254,147.32,'D','W'),
			array(0,135.97466666667,147.32,'E','W'),

			array(0,101.092,151.29933333333,'A','W'),
			array(0,109.81266666667,151.29933333333,'B','W'),
			array(0,118.53333333333,151.29933333333,'C','W'),
			array(0,127.254,151.384,'D','W'),
			array(0,135.97466666667,151.384,'E','W'),

			array(0,101.092,155.36333333333,'A','W'),
			array(0,109.81266666667,155.36333333333,'B','W'),
			array(0,118.53333333333,155.36333333333,'C','W'),
			array(0,127.254,155.36333333333,'D','W'),
			array(0,135.97466666667,155.36333333333,'E','W'),

			array(0,101.092,159.34266666667,'A','W'),
			array(0,109.81266666667,159.34266666667,'B','W'),
			array(0,118.53333333333,159.34266666667,'C','W'),
			array(0,127.254,159.34266666667,'D','W'),
			array(0,135.97466666667,159.34266666667,'E','W'),

			array(0,101.092,163.322,'A','W'),
			array(0,109.81266666667,163.322,'B','W'),
			array(0,118.53333333333,163.322,'C','W'),
			array(0,127.254,163.322,'D','W'),
			array(0,135.97466666667,163.322,'E','W'),

			array(0,101.092,167.30133333333,'A','W'),
			array(0,109.81266666667,167.30133333333,'B','W'),
			array(0,118.53333333333,167.30133333333,'C','W'),
			array(0,127.254,167.30133333333,'D','W'),
			array(0,135.97466666667,167.30133333333,'E','W'),

			array(0,101.092,171.36533333333,'A','W'),
			array(0,109.81266666667,171.36533333333,'B','W'),
			array(0,118.53333333333,171.36533333333,'C','W'),
			array(0,127.254,171.36533333333,'D','W'),
			array(0,135.97466666667,171.36533333333,'E','W'),

			array(0,101.092,175.34466666667,'A','W'),
			array(0,109.81266666667,175.34466666667,'B','W'),
			array(0,118.53333333333,175.34466666667,'C','W'),
			array(0,127.254,175.34466666667,'D','W'),
			array(0,135.97466666667,175.34466666667,'E','W'),

			array(0,101.092,179.324,'A','W'),
			array(0,109.81266666667,179.324,'B','W'),
			array(0,118.53333333333,179.324,'C','W'),
			array(0,127.254,179.324,'D','W'),
			array(0,135.97466666667,179.324,'E','W'),

			array(0,101.092,183.30333333333,'A','W'),
			array(0,109.81266666667,183.30333333333,'B','W'),
			array(0,118.53333333333,183.30333333333,'C','W'),
			array(0,127.254,183.30333333333,'D','W'),
			array(0,135.97466666667,183.30333333333,'E','W'),

			array(0,101.092,187.28266666667,'A','W'),
			array(0,109.81266666667,187.28266666667,'B','W'),
			array(0,118.53333333333,187.28266666667,'C','W'),
			array(0,127.254,187.36733333333,'D','W'),
			array(0,135.97466666667,187.28266666667,'E','W'),

			array(0,101.092,191.34666666667,'A','W'),
			array(0,109.81266666667,191.34666666667,'B','W'),
			array(0,118.53333333333,191.34666666667,'C','W'),
			array(0,127.254,191.34666666667,'D','W'),
			array(0,135.97466666667,191.34666666667,'E','W'),

			array(0,101.092,195.326,'A','W'),
			array(0,109.81266666667,195.326,'B','W'),
			array(0,118.53333333333,195.326,'C','W'),
			array(0,127.254,195.326,'D','W'),
			array(0,135.97466666667,195.326,'E','W'),

			array(0,101.092,199.30533333333,'A','W'),
			array(0,109.81266666667,199.30533333333,'B','W'),
			array(0,118.53333333333,199.30533333333,'C','W'),
			array(0,127.254,199.30533333333,'D','W'),
			array(0,135.97466666667,199.30533333333,'E','W'),

			array(0,101.092,203.28466666667,'A','W'),
			array(0,109.81266666667,203.28466666667,'B','W'),
			array(0,118.53333333333,203.28466666667,'C','W'),
			array(0,127.254,203.28466666667,'D','W'),
			array(0,135.97466666667,203.28466666667,'E','W'),

			array(0,101.092,207.34866666667,'A','W'),
			array(0,109.81266666667,207.34866666667,'B','W'),
			array(0,118.53333333333,207.34866666667,'C','W'),
			array(0,127.254,207.34866666667,'D','W'),
			array(0,135.97466666667,207.34866666667,'E','W'),

			array(0,101.092,211.328,'A','W'),
			array(0,109.81266666667,211.328,'B','W'),
			array(0,118.53333333333,211.328,'C','W'),
			array(0,127.254,211.328,'D','W'),
			array(0,135.97466666667,211.328,'E','W'),

			array(0,101.092,215.30733333333,'A','W'),
			array(0,109.81266666667,215.30733333333,'B','W'),
			array(0,118.53333333333,215.30733333333,'C','W'),
			array(0,127.254,215.30733333333,'D','W'),
			array(0,135.97466666667,215.30733333333,'E','W'),

			array(0,101.092,219.28666666667,'A','W'),
			array(0,109.81266666667,219.28666666667,'B','W'),
			array(0,118.53333333333,219.28666666667,'C','W'),
			array(0,127.254,219.28666666667,'D','W'),
			array(0,135.97466666667,219.28666666667,'E','W'),

			array(0,101.092,223.266,'A','W'),
			array(0,109.81266666667,223.266,'B','W'),
			array(0,118.53333333333,223.266,'C','W'),
			array(0,127.254,223.266,'D','W'),
			array(0,135.97466666667,223.35066666667,'E','W'),

			array(0,-37.930666666667,-59.605333333333,'A','W'),
			array(0,-29.21,-59.605333333333,'B','W'),
			array(0,-20.489333333333,-59.605333333333,'C','W'),
			array(0,-11.768666666667,-59.605333333333,'D','W'),
			array(0,-3.048,-59.605333333333,'E','W'),

			array(0,-37.930666666667,-55.626,'A','W'),
			array(0,-29.21,-55.626,'B','W'),
			array(0,-20.489333333333,-55.626,'C','W'),
			array(0,-11.768666666667,-55.626,'D','W'),
			array(0,-3.048,-55.626,'E','W'),

			array(0,-37.930666666667,-51.562,'A','W'),
			array(0,-29.21,-51.562,'B','W'),
			array(0,-20.489333333333,-51.562,'C','W'),
			array(0,-11.768666666667,-51.562,'D','W'),
			array(0,-3.048,-51.562,'E','W'),

			array(0,149.01333333333,139.36133333333,'A','W'),
			array(0,157.734,139.36133333333,'B','W'),
			array(0,166.45466666667,139.36133333333,'C','W'),
			array(0,175.17533333333,139.36133333333,'D','W'),
			array(0,183.896,139.36133333333,'E','W'),

			array(0,149.01333333333,143.34066666667,'A','W'),
			array(0,157.734,143.34066666667,'B','W'),
			array(0,166.45466666667,143.34066666667,'C','W'),
			array(0,175.17533333333,143.34066666667,'D','W'),
			array(0,183.896,143.34066666667,'E','W'),

			array(0,149.01333333333,147.32,'A','W'),
			array(0,157.734,147.32,'B','W'),
			array(0,166.45466666667,147.32,'C','W'),
			array(0,175.17533333333,147.32,'D','W'),
			array(0,183.896,147.32,'E','W'),

			array(0,149.01333333333,151.384,'A','W'),
			array(0,157.734,151.29933333333,'B','W'),
			array(0,166.45466666667,151.29933333333,'C','W'),
			array(0,175.17533333333,151.29933333333,'D','W'),
			array(0,183.896,151.29933333333,'E','W'),

			array(0,149.01333333333,155.36333333333,'A','W'),
			array(0,157.734,155.36333333333,'B','W'),
			array(0,166.45466666667,155.36333333333,'C','W'),
			array(0,175.17533333333,155.36333333333,'D','W'),
			array(0,183.896,155.36333333333,'E','W'),

			array(0,149.01333333333,159.34266666667,'A','W'),
			array(0,157.734,159.34266666667,'B','W'),
			array(0,166.45466666667,159.34266666667,'C','W'),
			array(0,175.17533333333,159.34266666667,'D','W'),
			array(0,183.896,159.34266666667,'E','W'),

			array(0,149.01333333333,163.322,'A','W'),
			array(0,157.734,163.322,'B','W'),
			array(0,166.45466666667,163.322,'C','W'),
			array(0,175.17533333333,163.322,'D','W'),
			array(0,183.896,163.322,'E','W'),

			array(0,149.01333333333,167.30133333333,'A','W'),
			array(0,157.734,167.30133333333,'B','W'),
			array(0,166.45466666667,167.30133333333,'C','W'),
			array(0,175.17533333333,167.30133333333,'D','W'),
			array(0,183.896,167.30133333333,'E','W'),

			array(0,149.01333333333,171.36533333333,'A','W'),
			array(0,157.734,171.36533333333,'B','W'),
			array(0,166.45466666667,171.36533333333,'C','W'),
			array(0,175.17533333333,171.36533333333,'D','W'),
			array(0,183.896,171.36533333333,'E','W'),

			array(0,149.01333333333,175.34466666667,'A','W'),
			array(0,157.734,175.34466666667,'B','W'),
			array(0,166.45466666667,175.34466666667,'C','W'),
			array(0,175.17533333333,175.34466666667,'D','W'),
			array(0,183.896,175.34466666667,'E','W'),

			array(0,149.01333333333,179.324,'A','W'),
			array(0,157.734,179.324,'B','W'),
			array(0,166.45466666667,179.324,'C','W'),
			array(0,175.17533333333,179.324,'D','W'),
			array(0,183.896,179.324,'E','W'),

			array(0,149.01333333333,183.30333333333,'A','W'),
			array(0,157.734,183.30333333333,'B','W'),
			array(0,166.45466666667,183.30333333333,'C','W'),
			array(0,175.17533333333,183.30333333333,'D','W'),
			array(0,183.896,183.30333333333,'E','W'),

			array(0,149.01333333333,187.28266666667,'A','W'),
			array(0,157.734,187.28266666667,'B','W'),
			array(0,166.45466666667,187.28266666667,'C','W'),
			array(0,175.17533333333,187.28266666667,'D','W'),
			array(0,183.896,187.28266666667,'E','W'),

			array(0,149.01333333333,191.34666666667,'A','W'),
			array(0,157.734,191.34666666667,'B','W'),
			array(0,166.45466666667,191.34666666667,'C','W'),
			array(0,175.17533333333,191.34666666667,'D','W'),
			array(0,183.896,191.34666666667,'E','W'),

			array(0,149.01333333333,195.326,'A','W'),
			array(0,157.734,195.326,'B','W'),
			array(0,166.45466666667,195.326,'C','W'),
			array(0,175.17533333333,195.326,'D','W'),
			array(0,183.896,195.326,'E','W'),

			array(0,149.01333333333,199.30533333333,'A','W'),
			array(0,157.734,199.30533333333,'B','W'),
			array(0,166.45466666667,199.30533333333,'C','W'),
			array(0,175.17533333333,199.30533333333,'D','W'),
			array(0,183.896,199.30533333333,'E','W'),

			array(0,149.01333333333,203.28466666667,'A','W'),
			array(0,157.734,203.28466666667,'B','W'),
			array(0,166.45466666667,203.28466666667,'C','W'),
			array(0,175.17533333333,203.28466666667,'D','W'),
			array(0,183.896,203.28466666667,'E','W'),

			array(0,149.01333333333,207.34866666667,'A','W'),
			array(0,157.734,207.34866666667,'B','W'),
			array(0,166.45466666667,207.34866666667,'C','W'),
			array(0,175.17533333333,207.34866666667,'D','W'),
			array(0,183.896,207.34866666667,'E','W'),

			array(0,149.01333333333,211.328,'A','W'),
			array(0,157.734,211.328,'B','W'),
			array(0,166.45466666667,211.328,'C','W'),
			array(0,175.17533333333,211.328,'D','W'),
			array(0,183.896,211.328,'E','W'),

			array(0,149.01333333333,215.30733333333,'A','W'),
			array(0,157.734,215.30733333333,'B','W'),
			array(0,166.45466666667,215.30733333333,'C','W'),
			array(0,175.17533333333,215.30733333333,'D','W'),
			array(0,183.896,215.30733333333,'E','W'),

			array(0,149.01333333333,219.28666666667,'A','W'),
			array(0,157.734,219.28666666667,'B','W'),
			array(0,166.45466666667,219.28666666667,'C','W'),
			array(0,175.17533333333,219.28666666667,'D','W'),
			array(0,183.896,219.28666666667,'E','W'),

			array(0,149.01333333333,223.266,'A','W'),
			array(0,157.734,223.266,'B','W'),
			array(0,166.45466666667,223.266,'C','W'),
			array(0,175.17533333333,223.266,'D','W'),
			array(0,183.896,223.266,'E','W'),



	),
);