# WriteUP · Level 8


考察 构造函数 (`__construct()`) 和 析构函数 (`__destruct()`) ，并且引入了一些 PHP垃圾回收机制的知识点 —— 请注意，GC机制和析构函数息息相关。

构造函数只会在类实例化的时候 —— 也就是使用 new 的方法手动创建对象的时候才会触发，而通过反序列化创建的对象不会触发这一方法，这也是为什么，在前面的内容，我将反序列化的对象创建过程称作为 “**还原**”。

析构函数会在对象被回收的时候触发 —— 手动回收和自动回收。

手动回收：就是代码中演示的 unset 方法用于释放对象。

自动回收：对象没有值引用指向，或者脚本结束完全释放，具体看题目中的演示结合该部分文字应该不难理解。

题目要求 全局变量 标识符flag的值大于5，根据 __destruct() 和 PHP GC 的特性，我们可以不断地去序列化和反序列化一个对象，然后不给该对象具体的引用以触发自动销毁机制。

**POST：**

code=`unserialize(serialize(unserialize(serialize(unserialize(serialize(unserialize(serialize(new RELFLAG()))))))));`

