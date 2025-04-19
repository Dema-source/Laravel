@extends('layout')

@section('title', 'عرض الكتاب')

@section('content')
<h5>Book Content:</h5>
<div id="content">
    @foreach ($sentences as $sentence)
    <span class="sentence">{{ $sentence }}</span> <!-- كل جملة في span -->
    @endforeach
</div>
<button id="play-button">Play</button>
@endsection

@section('styles')
<style>
    .highlight {
        color: black;
        font-weight: bold;
    }

</style>
@endsection

@section('scripts')
<script src="https://code.responsivevoice.org/responsivevoice.js?key=5sVXOg1F"></script>
<script>
    const sentences = document.querySelectorAll('.sentence');
    let currentSentence = 0;
    // Listener event
    document.getElementById('play-button').addEventListener('click', function() {
        const textContent = Array.from(sentences).map(sentence => sentence.textContent).join(' ');
        responsiveVoice.speak(textContent, "UK English Female",
        // {
        //    onstart: function() {
         //       highlightSentence();
        //    }
         //   , onend: function() {
           //     resetHighlights();
          //      currentSentence = 0;
        //    }
   //     }
        );
    });

    function highlightSentence() {
        resetHighlights();
        if (currentSentence < sentences.length) {
            const sentence = sentences[currentSentence];
            // add highlight
            sentence.classList.add('highlight');
            const duration = 10;
            // move to the next setnence
            setTimeout(() => {
                //remove highlight
                sentence.classList.remove('highlight'); 
                currentSentence++; 
                highlightSentence();  
            }, duration);
        }
    }

    function resetHighlights() {
        sentences.forEach(sentence => {
            sentence.classList.remove('highlight'); 
        });
    }

</script>
@endsection
