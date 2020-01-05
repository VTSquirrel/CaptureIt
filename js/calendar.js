function getCurrentDate(){
                var today = new Date();
                var dd = today.getDate();
                var mm = today.getMonth()+1; //January is 0!
                var yyyy = today.getFullYear();

                if(dd<10) {
                    dd = '0'+dd
                } 

                if(mm<10) {
                    mm = '0'+mm
                } 

                today = yyyy + '/' + mm + '/' + dd;
                return today;
              }
              $('#calendar').fullCalendar({
                themeSystem: 'bootstrap4',
                buttonText : {
                    prev : '<',
                    next : '>'
                },
                header: {
                  left: 'prev,next, today',
                  center: 'title',
                  right: 'month,agendaWeek,agendaDay,listMonth'
                },
                defaultView: 'month',
                height: 'parent',
                defaultDate: getCurrentDate(),
                navLinks: true, 
                eventLimit: true,
                eventSources: [
                  {
                    url: "getcalendardata.php",
                    type: "POST",
                    error: function() {
                      alert('there was an error while fetching events!');
                    },
                    color: '#00529F', 
                    textColor: 'white'
                  }
                ]
              });