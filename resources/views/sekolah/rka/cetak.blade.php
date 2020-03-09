<table border="1">
	<tr>
		<td>hello</td>
	</tr>

<?php
	foreach ($hasil as $i => $parent) {
		?>
		<tr>
			<td>Parent ke {{$i}}</td>
		</tr>
		<tr>
			<td>Parent Name</td>
			<?php
			foreach ($parent as $j => $program) {
				?>
				<td>Program ke {{$j}}</td>
				<?php
				foreach ($program as $j => $value) {
					# code...
				}
			}
			?>
		</tr>
		<?php
	}
?>
</table>