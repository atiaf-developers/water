<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends MyModel {

    protected $table = 'contact_messages';
    public static $types = ['suggestion', 'complaint'];
    public $casts = ['type'=>'integer'];

    public function reply() {
        return $this->hasMany(ContactMessageReply::class, 'contact_message_id');
    }

    public static function getAllApi($where_array = array()) {
        $contact_messages = static::join('contact_messages_reply', 'contact_messages.id', '=', 'contact_messages_reply.contact_message_id');
        $contact_messages->select('contact_messages.id', 'contact_messages.type', 'contact_messages.message', 'contact_messages_reply.message as reply', 'contact_messages_reply.created_at');
        $contact_messages->where('contact_messages.user_id', $where_array['user_id']);
        if (isset($where_array['contact_message_id'])) {
            $contact_messages->where('contact_messages.id', $where_array['contact_message_id']);
            $contact_messages = $contact_messages->first();
            if ($contact_messages) {
                $contact_messages = static::transformApi($contact_messages);
            }
        } else {
            $contact_messages->orderBy('contact_messages_reply.created_at', 'DESC');
            $contact_messages = $contact_messages->paginate(static::$limit);
            $contact_messages = $contact_messages->getCollection()->transform(function($contact_message, $key) {
                return static::transformApi($contact_message);
            });
        }
        return $contact_messages;
    }

    public static function transformApi($item) {

        $item->type_no = $item->type;
        $item->type_text = isset(static::$types[$item->type]) ? _lang('app.' . static::$types[$item->type]) : '';
        unset($item->type);
        return $item;
    }

    protected static function boot() {
        parent::boot();

        static::deleting(function($contact_message) {
            foreach ($contact_message->reply as $reply) {
                $reply->delete();
            }
        });

        static::deleted(function($category) {
            // Category::deleteUploaded('categories', $category->image);
        });
    }

}
