<?php /*pr($resultados);*/ ?>
<h2>Os Meus Calendários</h2>


<hr />
<div style="float:right;padding-top:5px;">

</div>
<div style="float:right">

<select id="sala" name="sala">
	<option value="-">- Escolha um calendário - </option>
    <?php foreach($calendarios as $c) { ?>
    <option value="<?php echo $c; ?>"><?php echo $c; ?></option>
    <?php } ?>
</select>
</div>
<br />
<br />
<br />
<!--<button class="btnCI btnCI-mini btnCI-success" onclick="dialogas('/servicosCake/servicoWeb/Calendario/createAnEvent', 'Criar Evento', 400,300)">Adicionar</button>
<br /><br /><br />-->
<script type="text/javascript" src="/servicosCake/servicoWeb/js/fullcalendar-1.5.4/fullcalendar/fullcalendar.js"></script>
<link href="/servicosCake/servicoWeb/js/fullcalendar-1.5.4/fullcalendar/fullcalendar.css" type="text/css" rel="stylesheet">
<script type="text/javascript" src="/servicosCake/servicoWeb/js/datepickerwithtime.js"></script>
<script type="text/javascript">
$("#sala").change(function() {
	  var selectedSite = $('#sala').val();
	  if(selectedSite.length > 1) {
      var events = {
                  url: '/servicosCake/servicoWeb/Calendario/data?feed=on',
                  type: 'POST',
                  data: {
                    sala: selectedSite
                  }
     }
     $('#calendar').fullCalendar('removeEventSource', events);
     $('#calendar').fullCalendar('addEventSource', events);
     $('#calendar').fullCalendar('refetchEvents');
	  }
	
});

		$(document).ready(function() {
	
		var date = new Date();
		var d = date.getDate();
		var m = date.getMonth();
		var y = date.getFullYear();
		
		$('#calendar').fullCalendar({
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,basicWeek,basicDay'
			},
			defaultView: 'agendaWeek',
			weekends: false,
			editable: false,
			allDaySlot: false,
			firstHour: 8,
			minTime:8,
			maxTime:20,
			columnFormat: {
					   month: 'ddd',    // Mon
   					   week: 'ddd', // Mon 9/7
  					   day: 'dddd'  // Monday 9/7
				},
			axisFormat: 'HH:mm',
			dayNamesShort: ['Domingo', 'Segunda', 'Terca', 'Quarta', 'Quinta', 'Sexta', 'S&aacute;bado'],
			timeFormat: 'H:mm{ - H:mm}', // uppercase H for 24-hour clock
			eventSources: [
				{
					url: '/servicosCake/servicoWeb/Calendario/data?feed=on', // use the `url` property
					color: 'black',    // an option!
					textColor: 'white'  // an option!
				}
			],
			eventRender: function(event, element) { 
		        element.find('.fc-event-title').css("font-size", "12px"); 
			},
			eventClick: function(calEvent, jsEvent, view) {
				var start_date = $.datepicker.formatDate('yy-mm-dd', calEvent.start);
				dialogas("/servicosCake/servicoWeb/Calendario/details?data=" + start_date + "&unique_id=" + calEvent.unique_id + "&sala=" + $("#sala").val(), "Detalhes", 600, 250);
			},
		});
		
	});

$(document).ready(function(){
});
</script>
<div id='calendar'></div>
<script>
//var view = $('#calendar').fullCalendar('getView');
//console.log(view);
</script>