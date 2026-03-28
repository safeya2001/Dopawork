@php
$map = [
    'pending'      => ['bg-yellow-100 text-yellow-800', app()->getLocale()==='ar' ? 'معلق' : 'Pending'],
    'in_progress'  => ['bg-blue-100 text-blue-800',   app()->getLocale()==='ar' ? 'جاري' : 'In Progress'],
    'delivered'    => ['bg-indigo-100 text-indigo-800', app()->getLocale()==='ar' ? 'مسلّم' : 'Delivered'],
    'revision'     => ['bg-orange-100 text-orange-800', app()->getLocale()==='ar' ? 'تعديل' : 'Revision'],
    'completed'    => ['bg-green-100 text-green-800',  app()->getLocale()==='ar' ? 'مكتمل' : 'Completed'],
    'cancelled'    => ['bg-red-100 text-red-800',      app()->getLocale()==='ar' ? 'ملغى' : 'Cancelled'],
    'disputed'     => ['bg-purple-100 text-purple-800', app()->getLocale()==='ar' ? 'نزاع' : 'Disputed'],
    'active'       => ['bg-green-100 text-green-800',  app()->getLocale()==='ar' ? 'نشط' : 'Active'],
    'inactive'     => ['bg-gray-100 text-gray-600',    app()->getLocale()==='ar' ? 'غير نشط' : 'Inactive'],
    'suspended'    => ['bg-red-100 text-red-800',      app()->getLocale()==='ar' ? 'موقوف' : 'Suspended'],
    'pending_verification' => ['bg-yellow-100 text-yellow-800', app()->getLocale()==='ar' ? 'انتظار التحقق' : 'Pending Verification'],
    'approved'     => ['bg-green-100 text-green-800',  app()->getLocale()==='ar' ? 'موافق' : 'Approved'],
    'rejected'     => ['bg-red-100 text-red-800',      app()->getLocale()==='ar' ? 'مرفوض' : 'Rejected'],
    'open'         => ['bg-blue-100 text-blue-800',    app()->getLocale()==='ar' ? 'مفتوح' : 'Open'],
    'resolved'     => ['bg-green-100 text-green-800',  app()->getLocale()==='ar' ? 'محلول' : 'Resolved'],
    'held'         => ['bg-yellow-100 text-yellow-800', app()->getLocale()==='ar' ? 'محتجز' : 'Held'],
    'released'     => ['bg-green-100 text-green-800',  app()->getLocale()==='ar' ? 'مُصرف' : 'Released'],
    'refunded'     => ['bg-orange-100 text-orange-800', app()->getLocale()==='ar' ? 'مسترد' : 'Refunded'],
];
[$cls, $label] = $map[$status] ?? ['bg-gray-100 text-gray-600', ucfirst(str_replace('_', ' ', $status))];
@endphp
<span class="inline-block text-xs font-medium px-2.5 py-0.5 rounded-full {{ $cls }}">{{ $label }}</span>
