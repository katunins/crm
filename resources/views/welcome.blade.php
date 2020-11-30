@extends('app')
@section('content')
    <div class="containter">
        @if (Session::has('info'))
            <div class="info-block">
                {{ Session::get('info') }}
            </div>
        @endif
        <div class="input-block">
            <form action="setNewTask" method="POST">
                @csrf
                @error('name')
                    <div class="error">{{ $message }}</div>
                @enderror
                <input type="text" name="name" placeholder="Начните писать задачу ..." oninput="checkFilter()"
                    autocomplete="off">
                <input class="hide" type="text" name="description" id="" placeholder="Краткое описание ..." autocomplete="off">
                <div class="bottom-buttons">
                    <input type="submit" value="+">
                    <input type="button" value="x" onclick="clearInput()">
                </div>
            </form>
        </div>
        <div class="list-block">
            @foreach ($tasks as $item)
                <div class="task-group">
                    <div class="like-button">
                        <button onclick="setLike({{ $item->id }})" likes={{ $item->likes }}>
                            {{ $item->likes }}
                        </button>
                    </div>
                    <div class="text-block">
                        <div class="task-name">{{ $item->name }}</div>
                        @if ($item->description != '')
                            <div class="description">{{ $item->description }}</div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection

<script>
    function ajax(
        url,
        data,
        callBack = null
    ) {
        fetch(url, {
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json, text-plain, */*',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute('content'),
                },
                method: 'post',
                credentials: 'same-origin',
                body: JSON.stringify(data),
            })
            .then(response => response.json())
            .then(response => {
                if (callBack) callBack(response);
                // console.log (callBack);
            })
            .catch(function(error) {
                console.log(error);
            });
    };

    function setLike(id) {
        let likeElem = event.target
        ajax('setLike', {
            id: id
        }, function(result) {
            if (result) {
                let oldCount = likeElem.getAttribute('likes')
                let newCount = Number(oldCount) + 1
                likeElem.setAttribute('likes', newCount)
                likeElem.innerHTML = newCount
            }
        })
    }

    function checkFilter() {
        let inputText = document.querySelector('input[name="name"]').value
        let descriptionElem = document.querySelector('input[name="description"]')

        console.log (inputText == '')
        if (inputText == '') {
            descriptionElem.classList.add('hide')
        } else {
            if (descriptionElem.classList.contains('hide')) descriptionElem.classList.remove('hide');
        }

        document.querySelectorAll('.task-name').forEach(el => {
            if (el.innerHTML.toLowerCase().indexOf(inputText.toLowerCase()) >= 0 && inputText.length > 1) {
                el.classList.add('activeFilter')
            } else {
                if (el.classList.contains('activeFilter')) el.classList.remove('activeFilter');
            }

        })
    }

    function clearInput() {
        document.querySelector('input[name="name"]').value = ''
        checkFilter()
    }

</script>
