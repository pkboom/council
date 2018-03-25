{{-- Editing the question. --}}
<div class="panel panel-default" v-if="editing">
    <div class="panel-heading">
        <div class="level">
            <input type="text" class="form-control" v-model="form.title">
        </div>
    </div>

    <div class="panel-body">
        <div class="form-group">
            {{--  <textarea rows="10" class="form-control" v-model="form.body"></textarea>  --}}
            <wysiwyg :value="form.body"></wysiwyg>
        </div>
    </div>

    <div class="panel-footer">
        <div class="level">
            {{--  <button class="btn btn-xs mr-1" @click="editing = true" v-show="! editing">Edit</button>  --}}
            <button class="btn btn-primary btn-xs mr-1" @click="update">Update</button>
            <button class="btn btn-xs mr-1" @click="resetForm">Cancel</button>

            <form action="{{ $thread->path() }}" method="post" class="ml-a">
                {{ csrf_field() }}
                {{ method_field('delete') }}
    
               <button type="submit" class="btn btn-link">Delete Thread</button>
            </form>
        </div>

    </div>
</div>

{{--  Viewing the question.  --}}
<div class="panel panel-default" v-else>
    <div class="panel-heading">
        <div class="level">
            <img src="{{ $thread->creator->avatar_path }}" 
                alt="{{ $thread->creator->name }}" 
                width="25" 
                height="25" 
                class="mr-1">

            <span class="flex">
                <a href="{{ route('profile', $thread->creator) }}">
                    {{ $thread->creator->name }} ({{ $thread->creator->reputation }} XP)
                </a> posted:<span v-text="title"></span>
            </span>
        </div>
    </div>

    <div class="panel-body" v-html="body"></div> 

    <div class="panel-footer" v-if="authorize('owns', thread)">
        <button class="btn btn-xs" @click="editing = true">Edit</button>
    </div>
</div>

